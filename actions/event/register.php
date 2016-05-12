<?php

elgg_make_sticky_form('event_register');

$guid = (int) get_input('event_guid');
$relation = get_input('relation');
$register_type = get_input('register_type');
$program_guids = get_input('program_guids');

elgg_entity_gatekeeper($guid, 'object', Event::SUBTYPE);
$event = get_entity($guid);

if (empty($relation)) {
	register_error(elgg_echo('error:missing_data'));
	forward(REFERER);
}

$answers = [];
foreach ($_POST as $key => $value) {
	$value = get_input($key);
	if (substr($key, 0, 9) == 'question_') {
		if (is_array($value)) {
			$value = $value[0];
		}

		$answers[substr($key, 9, strlen($key))] = $value;
	}
}

$user = elgg_get_logged_in_user_entity();
$required_error = false;

if ($questions = $event->getRegistrationFormQuestions()) {
	foreach ($questions as $question) {
		if ($question->required && empty($answers[$question->getGUID()]) && ($answers[$question->getGUID()] !== '0')) {
			$required_error = true;
		}

		if (empty($user)) {
			if (empty($answers['name']) || empty($answers['email'])) {
				$required_error = true;
			}
		}
	}
}

if ($event->with_program && !$required_error) {
	if (empty($program_guids)) {
		$required_error = true;
	} else {
		// validate slot sets
		$slot_options = [
			'type' => 'object',
			'subtype' => \ColdTrick\EventManager\Event\Slot::SUBTYPE,
			'limit' => false,
			'metadata_names' => 'slot_set',
			'guids' => explode(',', $program_guids),
		];

		if ($set_metadata = elgg_get_metadata($slot_options)) {
			$sets_found = [];
			foreach ($set_metadata as $md) {
				$set_name = $md->value;
				if (in_array($set_name, $sets_found)) {
					// only one programguid per slot is allowed
					register_error(elgg_echo('event_manager:action:registration:edit:error_slots', [$set_name]));
					forward(REFERER);
				}
				$sets_found[] = $set_name;
			}
		}
	}
}

if ($required_error) {
	if ($event->with_program) {
		if ($questions) {
			register_error(elgg_echo('event_manager:action:registration:edit:error_fields_with_program'));
		} else {
			register_error(elgg_echo('event_manager:action:registration:edit:error_fields_program_only'));
		}
	} else {
		register_error(elgg_echo('event_manager:action:event:edit:error_fields'));
	}

	forward(REFERER);
}

if (elgg_is_logged_in()) {
	$object = elgg_get_logged_in_user_entity();
} else {
	// validate email
	$old_ia = elgg_set_ignore_access(true);
	$object = null;
	
	if (!is_email_address($answers['email'])) {
		register_error(elgg_echo('registration:notemail'));
		forward(REFERER);
	} else {
		// check for user with this emailaddress
		if ($existing_user = get_user_by_email($answers['email'])) {
			$object = $existing_user[0];
			// todo check if there already is a relationship with the event.
			$current_relationship = $event->getRelationshipByUser($object->getGUID());
			if ($current_relationship) {
				switch ($current_relationship) {
					case EVENT_MANAGER_RELATION_ATTENDING:
						// already attendee
						register_error(elgg_echo('event_manager:action:register:email:account_exists:attending'));
						forward(REFERER);
					case EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST:
						// on the waitinglist
						register_error(elgg_echo('event_manager:action:register:email:account_exists:waiting'));
						forward();
					case EVENT_MANAGER_RELATION_ATTENDING_PENDING:
						// pending confirmation resend mail
						event_manager_send_registration_validation_email($event, $object);

						register_error(elgg_echo('event_manager:action:register:email:account_exists:pending'));
						forward(REFERER);
				}
			}
		}

		// check for existing registration based on this email
		$existing_entities = elgg_get_entities_from_metadata([
			'type' => 'object',
			'subtype' => EventRegistration::SUBTYPE,
			'owner_guid' => $event->getGUID(),
			'metadata_name_value_pairs' => ['email' => $answers['email']],
			'metadata_case_sensitive' => false,
		]);
		
		if ($existing_entities) {
			$object = $existing_entities[0];

			$current_relationship = $event->getRelationshipByUser($object->getGUID());
			if ($current_relationship) {
				switch ($current_relationship) {
					case EVENT_MANAGER_RELATION_ATTENDING:
						// already attendee
						register_error(elgg_echo("event_manager:action:register:email:account_exists:attending"));
						forward(REFERER);
					case EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST:
						// on the waitinglist
						register_error(elgg_echo("event_manager:action:register:email:account_exists:waiting"));
						forward(REFERER);
					case EVENT_MANAGER_RELATION_ATTENDING_PENDING:
					default:
						// pending confirmation resend mail
						event_manager_send_registration_validation_email($event, $object);

						register_error(elgg_echo("event_manager:action:register:email:account_exists:pending"));
						forward(REFERER);
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
	system_message(elgg_echo('event_manager:action:register:pending'));
}

// relate to the event
if (!$event->rsvp($relation, $object->getGUID())) {
	register_error(elgg_echo('event_manager:event:relationship:message:error'));
	forward(REFERER);
}

elgg_clear_sticky_form('event_register');

if (elgg_is_logged_in()) {
	forward("events/registration/completed/{$event->getGUID()}/{$object->getGUID()}/" . elgg_get_friendly_title($event->title));
}

forward($event->getURL());
