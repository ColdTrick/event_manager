<?php

	$guid = get_input("event_guid");
 	$relation = get_input("relation");
	
	$register_type = get_input('register_type');
	
	$program_guids = get_input('program_guids');
	
	$answers = array();
	foreach($_POST as $key => $value) {		
		$value = get_input($key);
		if(substr($key, 0, 9) == 'question_') {
			if(is_array($value)) {
				$value = $value[0];
			}
			
			$answers[substr($key, 9, strlen($key))] = $value;
		}
	}
	
	$forward_url = REFERER;
	
	if(!empty($guid) && !empty($relation) && ($entity = get_entity($guid)))	{
		if($entity instanceof Event) {
			$event = $entity;
			
			if($event) {	
				$user = elgg_get_logged_in_user_entity();
				
				$questions = $event->getRegistrationFormQuestions();
				foreach($questions as $question) {
					if($question->required && empty($answers[$question->getGUID()])) {
						$required_error = true;
					}
					
					if(!elgg_is_logged_in()) {
						if(empty($answers['name']) || empty($answers['email'])) {
							$required_error = true;
						}
					}
					
					$_SESSION['registerevent_values']['question_'.$question->getGUID()]	= $answers[$question->getGUID()];
				}
				
				// @todo: replace with sticky form functions
				// @todo: make program also sticky
				
				if(empty($user)) {
					$_SESSION['registerevent_values']['question_name']	= $answers["name"];
					$_SESSION['registerevent_values']['question_email']	= $answers["email"];
				}
				
				if($event->with_program && !$required_error) {
					if(empty($program_guids)) {
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
						
						if($set_metadata = elgg_get_metadata($slot_options)){
							$sets_found = array();
							foreach($set_metadata as $md){
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
				
				if($required_error)	{
					if($event->with_program) {
						if($questions) {
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
				
				if(elgg_is_logged_in()) {
					$object = elgg_get_logged_in_user_entity();
				} else {
					// validate email
					$old_ia = elgg_set_ignore_access(true);
					
					if(!is_email_address($answers["email"])){
						register_error(elgg_echo("registration:notemail"));
						forward($forward_url);
					} else {
						
						if(get_user_by_email($answers["email"])){
							// check for user with this emailaddress
							
							register_error(elgg_echo("event_manager:action:register:email:account_exists"));
							forward($forward_url);
						} else {
							// check for existing registration based on this email	
							$options = array(
									"type" => "object",
									"subtype" => EventRegistration::SUBTYPE,
									"owner_guid" => $event->getGUID(),
									"metadata_name_value_pairs" => array("email" => $answers["email"]),
									"metadata_case_sensitive" => false,
									"count" => TRUE
								);
							
							if(elgg_get_entities_from_metadata($options)){
								register_error(elgg_echo("event_manager:action:register:email:registration_exists"));
								forward($forward_url);
							}						
						}
					}
					
					// create new registration
					$object = new EventRegistration();
					$object->title = 'EventRegistrationNotLoggedinUser';
					$object->description = 'EventRegistrationNotLoggedinUser';
					$object->owner_guid = $event->getGUID();
					$object->container_guid = $event->getGUID();
					$object->access_id = ACCESS_PUBLIC;
					$object->save();
					
					elgg_set_ignore_access($old_ia);
				}				
				
				foreach($answers as $question_guid => $answer) {
					if(!empty($question_guid) && ($question = get_entity($question_guid))) {
						if($question instanceof EventRegistrationQuestion) {
							$question->updateAnswerFromUser($event, $answer, $object->getGUID());
						}
					} else {
						$object->{$question_guid} = $answer;
					}
				}

				$guid_explode = explode(',', $program_guids);
				
				if(elgg_is_logged_in()) {
					$event->relateToAllSlots(false);
				}
				
				foreach($guid_explode as $slot_guid) {
					if(!empty($slot_guid)) {
						if($register_type == 'waitinglist') {
							$relation = EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST;
							$slot_relation = EVENT_MANAGER_RELATION_SLOT_REGISTRATION_WAITINGLIST;
						} else {
							$slot_relation = EVENT_MANAGER_RELATION_SLOT_REGISTRATION;
						}
						
						$object->addRelationship($slot_guid, $slot_relation);
					}
				}
			
				if($event->rsvp($relation, $object->getGUID())) {
					$forward_url = "events/registration/completed/" . $event->getGUID() . "/" . $object->getGUID() . "/" . elgg_get_friendly_title($event->title);
				} else {
					$forward_url = $event->getURL();
					
					register_error(elgg_echo('event_manager:event:relationship:message:error'));
				}
			} else {	
				register_error(elgg_echo("event_manager:event_not_found"));
			}
		}
	} else {
		system_message(elgg_echo("InvalidParameterException:MissingParameter"));
	}
	
	forward($forward_url);
	