<?php

	$guid = get_input("event_guid");
 	$relation = get_input("relation");
	
	$register_type = get_input('register_type');
	
	$program_guids = get_input('program_guids');
	
	$answers = array();
	foreach ($_POST as $key => $value) {
		$value = get_input($key);
		if (substr($key, 0, 9) == 'question_') {
			if (is_array($value)) {
				$value = $value[0];
			}
			
			$answers[substr($key, 9, strlen($key))] = $value;
		}
	}
	
	$forward_url = REFERER;
	
	if (!empty($guid) && !empty($relation) && ($event = get_entity($guid))) {
		if ($event instanceof Event) {
			$user = elgg_get_logged_in_user_entity();
			
			if ($questions = $event->getRegistrationFormQuestions()) {
				foreach ($questions as $question) {
					if ($question->required && empty($answers[$question->getGUID()])) {
						$required_error = true;
					}
					
					if (empty($user)) {
						if (empty($answers['name']) || empty($answers['email'])) {
							$required_error = true;
						}
					}
					
					$_SESSION['registerevent_values']['question_' . $question->getGUID()] = $answers[$question->getGUID()];
				}
			}
			
			// @todo: replace with sticky form functions
			// @todo: make program also sticky
			
			if (empty($user)) {
				$_SESSION['registerevent_values']['question_name'] = $answers["name"];
				$_SESSION['registerevent_values']['question_email']	= $answers["email"];
			}
			
			if ($event->with_program && !$required_error) {
				if (empty($program_guids)) {
					$required_error = true;
				} else {
					// validate slot sets
					$slot_options = array(
							"type" => "object",
							"subtype" => EventSlot::SUBTYPE,
							"limit" => false,
							"metadata_names" => "slot_set",
							"guids" => explode(',', $program_guids)
					);
					
					if ($set_metadata = elgg_get_metadata($slot_options)) {
						$sets_found = array();
						foreach ($set_metadata as $md){
							$set_name = $md->value;
							if(in_array($set_name, $sets_found)){
								// only one programguid per slot is allowed
								register_error(elgg_echo("event_manager:action:registration:edit:error_slots", array($set_name)));
								forward($forward_url);
							}
							$sets_found[] = $set_name;
						}
					}
				}
			}
			
			if ($required_error)	{
				if ($event->with_program) {
					if ($questions) {
						register_error(elgg_echo("event_manager:action:registration:edit:error_fields_with_program"));
					} else {
						register_error(elgg_echo("event_manager:action:registration:edit:error_fields_program_only"));
					}
				} else {
					register_error(elgg_echo("event_manager:action:event:edit:error_fields"));
				}
				
				forward($forward_url);
			} else {
				$_SESSION['registerevent_values'] = null;
			}
			
			if (elgg_is_logged_in()) {
				$object = elgg_get_logged_in_user_entity();
			} else {
				// validate email
				$old_ia = elgg_set_ignore_access(true);
				
				if (!is_email_address($answers["email"])) {
					register_error(elgg_echo("registration:notemail"));
					forward($forward_url);
				} else {
					// check for user with this emailaddress
					if ($existing_user = get_user_by_email($answers["email"])) {
						$object = $existing_user[0];
						// todo check if there already is a relationship with the event.
						$current_relationship = $event->getRelationshipByUser($object->getGUID());
						if ($current_relationship) {
							switch ($current_relationship) {
								case EVENT_MANAGER_RELATION_ATTENDING:
									// already attendee
									register_error(elgg_echo("event_manager:action:register:email:account_exists:attending"));
									forward($forward_url);
								case EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST:
									// on the waitinglist
									register_error(elgg_echo("event_manager:action:register:email:account_exists:waiting"));
									forward($forward_url);
								case EVENT_MANAGER_RELATION_ATTENDING_PENDING:
									// pending confirmation resend mail
									event_manager_send_registration_validation_email($event, $object);
									
									register_error(elgg_echo("event_manager:action:register:email:account_exists:pending"));
									forward($forward_url);
							}
						}
					}
					
					// check for existing registration based on this email
					$options = array(
							"type" => "object",
							"subtype" => EventRegistration::SUBTYPE,
							"owner_guid" => $event->getGUID(),
							"metadata_name_value_pairs" => array("email" => $answers["email"]),
							"metadata_case_sensitive" => false
						);
					
					if ($existing_entities = elgg_get_entities_from_metadata($options)) {
						$object = $existing_entities[0];
						
						$current_relationship = $event->getRelationshipByUser($object->getGUID());
						if ($current_relationship) {
							switch ($current_relationship) {
								case EVENT_MANAGER_RELATION_ATTENDING:
									// already attendee
									register_error(elgg_echo("event_manager:action:register:email:account_exists:attending"));
									forward($forward_url);
								case EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST:
									// on the waitinglist
									register_error(elgg_echo("event_manager:action:register:email:account_exists:waiting"));
									forward($forward_url);
								case EVENT_MANAGER_RELATION_ATTENDING_PENDING:
								default:
									// pending confirmation resend mail
									event_manager_send_registration_validation_email($event, $object);
										
									register_error(elgg_echo("event_manager:action:register:email:account_exists:pending"));
									forward($forward_url);
							}
						}
					}
				}
				
				if (!$object) {
					// create new registration
					$object = new EventRegistration();
					$object->title = 'EventRegistrationNotLoggedinUser';
					$object->description = 'EventRegistrationNotLoggedinUser';
					$object->owner_guid = $event->getGUID();
					$object->container_guid = $event->getGUID();
					$object->save();
				}
				
				elgg_set_ignore_access($old_ia);
			}

			// save all answers
			foreach ($answers as $question_guid => $answer) {
				if (!empty($question_guid) && ($question = get_entity($question_guid))) {
					if ($question instanceof EventRegistrationQuestion) {
						$question->updateAnswerFromUser($event, $answer, $object->getGUID());
					}
				} else {
					$object->{$question_guid} = $answer;
				}
			}

			if (elgg_is_logged_in()) {
				// remove existing relations with slots
				$event->relateToAllSlots(false);
			}
			
			if (!elgg_is_logged_in()) {
				// change relationship to pending
				$relation = EVENT_MANAGER_RELATION_ATTENDING_PENDING;
				$slot_relation = EVENT_MANAGER_RELATION_SLOT_REGISTRATION_PENDING;
			} else {
				if ($register_type == 'waitinglist') {
					$relation = EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST;
					$slot_relation = EVENT_MANAGER_RELATION_SLOT_REGISTRATION_WAITINGLIST;
				} else {
					$slot_relation = EVENT_MANAGER_RELATION_SLOT_REGISTRATION;
				}
			}
			
			$guid_explode = explode(',', $program_guids);
			
			foreach ($guid_explode as $slot_guid) {
				// add relationships with slots
				if (!empty($slot_guid)) {
					$object->addRelationship($slot_guid, $slot_relation);
				}
			}
			
			if (!elgg_is_logged_in()) {
				
				event_manager_send_registration_validation_email($event, $object);
				system_message(elgg_echo("event_manager:action:register:pending"));
			}
			
			$forward_url = $event->getURL();
			if ($event->rsvp($relation, $object->getGUID())) {
				// relate to the event
				if (elgg_is_logged_in()) {
					$forward_url = "events/registration/completed/" . $event->getGUID() . "/" . $object->getGUID() . "/" . elgg_get_friendly_title($event->title);
				}
			} else {
				register_error(elgg_echo('event_manager:event:relationship:message:error'));
			}
		} else {
			register_error(elgg_echo("event_manager:event_not_found"));
		}
	} else {
		system_message(elgg_echo("InvalidParameterException:MissingParameter"));
	}
	
	forward($forward_url);
	