<?php

use Elgg\Http\ResponseBuilder;

elgg_make_sticky_form('event_register');

$guid = (int) get_input('event_guid');
$relation = get_input('relation');
$register_type = get_input('register_type');
$program_guids = get_input('program_guids');

elgg_entity_gatekeeper($guid, 'object', Event::SUBTYPE);
$event = get_entity($guid);

if (empty($relation)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
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
		if ($question->required && empty($answers[$question->guid]) && ($answers[$question->guid] !== '0')) {
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
		$set_metadata = elgg_get_metadata([
			'type' => 'object',
			'subtype' => \ColdTrick\EventManager\Event\Slot::SUBTYPE,
			'limit' => false,
			'metadata_names' => 'slot_set',
			'guids' => explode(',', $program_guids),
		]);

		if ($set_metadata) {
			$sets_found = [];
			foreach ($set_metadata as $md) {
				$set_name = $md->value;
				if (in_array($set_name, $sets_found)) {
					// only one programguid per slot is allowed
					return elgg_error_response(elgg_echo('event_manager:action:registration:edit:error_slots', [$set_name]));
				}
				$sets_found[] = $set_name;
			}
		}
	}
}

if ($required_error) {
	if ($event->with_program) {
		if ($questions) {
			$error_message = elgg_echo('event_manager:action:registration:edit:error_fields_with_program');
		} else {
			$error_message = elgg_echo('event_manager:action:registration:edit:error_fields_program_only');
		}
	} else {
		$error_message = elgg_echo('event_manager:action:event:edit:error_fields');
	}

	return elgg_error_response($error_message);
}

if (elgg_is_logged_in()) {
	$object = elgg_get_logged_in_user_entity();
} else {
	// validate email
	$object = elgg_call(ELGG_IGNORE_ACCESS, function() use ($answers, $event) {
		$object = null;
		
		if (!is_email_address($answers['email'])) {
			return elgg_error_response(elgg_echo('registration:notemail'));
		} else {
			// check for user with this emailaddress
			if ($existing_user = get_user_by_email($answers['email'])) {
				$object = $existing_user[0];
				// todo check if there already is a relationship with the event.
				$current_relationship = $event->getRelationshipByUser($object->guid);
				if ($current_relationship) {
					switch ($current_relationship) {
						case EVENT_MANAGER_RELATION_ATTENDING:
							// already attendee
							return elgg_error_response(elgg_echo('event_manager:action:register:email:account_exists:attending'));
						case EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST:
							// on the waitinglist
							return elgg_error_response(elgg_echo('event_manager:action:register:email:account_exists:waiting'));
						case EVENT_MANAGER_RELATION_ATTENDING_PENDING:
							// pending confirmation resend mail
							event_manager_send_registration_validation_email($event, $object);
	
							return elgg_error_response(elgg_echo('event_manager:action:register:email:account_exists:pending'));
					}
				}
			}
	
			// check for existing registration based on this email
			$existing_entities = elgg_get_entities([
				'type' => 'object',
				'subtype' => EventRegistration::SUBTYPE,
				'owner_guid' => $event->guid,
				'metadata_name_value_pairs' => ['email' => $answers['email']],
				'metadata_case_sensitive' => false,
			]);
			
			if ($existing_entities) {
				$object = $existing_entities[0];
	
				$current_relationship = $event->getRelationshipByUser($object->guid);
				if ($current_relationship) {
					switch ($current_relationship) {
						case EVENT_MANAGER_RELATION_ATTENDING:
							// already attendee
							return elgg_error_response(elgg_echo('event_manager:action:register:email:account_exists:attending'));
						case EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST:
							// on the waitinglist
							return elgg_error_response(elgg_echo('event_manager:action:register:email:account_exists:waiting'));
						case EVENT_MANAGER_RELATION_ATTENDING_PENDING:
						default:
							// pending confirmation resend mail
							event_manager_send_registration_validation_email($event, $object);
	
							return elgg_error_response(elgg_echo('event_manager:action:register:email:account_exists:pending'));
					}
				}
			}
		}
	
		if (!$object) {
			// create new registration
			$object = new EventRegistration();
			$object->title = 'EventRegistrationNotLoggedinUser';
			$object->description = 'EventRegistrationNotLoggedinUser';
			$object->owner_guid = $event->guid;
			$object->container_guid = $event->guid;
			$object->save();
		}
		
		return $object;
	});
	
	if ($object instanceof ResponseBuilder) {
		return $object;
	}
}

// save all answers
foreach ($answers as $question_guid => $answer) {
	if (!empty($question_guid) && ($question = get_entity($question_guid))) {
		if ($question instanceof EventRegistrationQuestion) {
			$question->updateAnswerFromUser($event, $answer, $object->guid);
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

$success_message = '';
if (!elgg_is_logged_in()) {
	event_manager_send_registration_validation_email($event, $object);
	$success_message = elgg_echo('event_manager:action:register:pending');
}

// relate to the event
if (!$event->rsvp($relation, $object->guid)) {
	return elgg_error_response(elgg_echo('event_manager:event:relationship:message:error'));
}

elgg_clear_sticky_form('event_register');

$forward_url = $event->getURL();
if (elgg_is_logged_in()) {
	$forward_url = elgg_generate_url('default:object:eventregistration:completed', [
		'event_guid' => $event->guid,
		'object_guid' => $object->guid,
	]);
}

return elgg_ok_response('', $success_message, $forward_url);
