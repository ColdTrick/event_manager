<?php

// start a new sticky form session in case of failure
elgg_make_sticky_form('event');

$title = get_input('title');

$event_start = \Elgg\Values::normalizeTime(gmdate('c', (int) get_input('event_start')));
$event_start->setTime(0,0,0);
$event_start = $event_start->getTimestamp();

$start_time = (int) get_input('start_time');

$event_end = \Elgg\Values::normalizeTime(gmdate('c', (int) get_input('event_end')));
$event_end->setTime(0,0,0);
$event_end = $event_end->getTimestamp();

$end_time = (int) get_input('end_time');

$endregistration_day = get_input('endregistration_day');

$access_id = (int) get_input('access_id');

if (empty($title) || empty($event_start) || empty($event_end)) {
	return elgg_error_response(elgg_echo('event_manager:action:event:edit:error_fields'));
}

$event_end += $end_time;

$event_start += $start_time;

if ($event_end < $event_start) {
	return elgg_error_response(elgg_echo('event_manager:action:event:edit:end_before_start'));
}

if (!empty($endregistration_day)) {
	$date_endregistration_day = \Elgg\Values::normalizeTime($endregistration_day);
	$date_endregistration_day->setTime(0, 0, 1);
	$endregistration_day = $date_endregistration_day->getTimestamp();
}

$entity = get_entity(get_input('guid'));
$event_created = false;
if ($entity instanceof \Event) {
	$event = $entity;
} else {
	$event_created = true;
	$event = new \Event();
}

$event->title = $title;
$event->description = get_input('description');
$event->container_guid = (int) get_input('container_guid');
$event->access_id = $access_id;
$event->save();

$event->location = get_input('location');
$event->setLatLong((float) get_input('latitude'), (float) get_input('longitude'));

$event->tags = string_to_tag_array(get_input('tags'));

if ($event_created) {
	elgg_create_river_item([
		'action_type' => 'create',
		'subject_guid' => elgg_get_logged_in_user_guid(),
		'object_guid' => $event->guid,
	]);
}

$event->setMaxAttendees(get_input('max_attendees'));

$event->event_start = $event_start;
$event->event_end = $event_end;

$event->with_program = get_input('with_program');
$event->endregistration_day = $endregistration_day;
$event->event_interested = 1;

$metadata_fields = [
	'shortdescription', 'comments_on', 'registration_ended', 'registration_needed', 'show_attendees',
	'notify_onsignup', 'notify_onsignup_contact', 'notify_onsignup_organizer', 'venue', 'contact_details', 'website',
	'organizer', 'fee', 'fee_details', 'register_nologin', 'waiting_list_enabled', 'registration_completed',
	'organizer_guids', 'contact_guids', 'region', 'event_type',
];

foreach ($metadata_fields as $field) {
	$event->{$field} = get_input($field);
}

$has_days = $event->hasEventDays();
$event->generateInitialProgramData();

if (get_input('icon_remove')) {
	$event->deleteIcon();
} elseif ($uploaded_file = elgg_get_uploaded_file('icon')) {
	if (stripos($uploaded_file->getMimeType(), 'image/') !== false) {
		$event->saveIconFromUploadedFile('icon');
	}
}

elgg_call(ELGG_IGNORE_ACCESS, function() use ($event) {
	$order = 0;
	
	$questions = get_input('questions');
	$saved_questions = [];
	if (!empty($questions)) {
		foreach ($questions as $question) {
			$question_guid = (int) elgg_extract('guid', $question);
			$fieldtype = elgg_extract('fieldtype', $question);
			$fieldoptions = elgg_extract('fieldoptions', $question);
			$questiontext = elgg_extract('questiontext', $question);
			$required = elgg_extract('required', $question);
			$required = !empty($required) ? 1 : 0;
			
			if ($question_guid) {
				$question = get_entity($question_guid);
				if (!($question instanceof \EventRegistrationQuestion)) {
					continue;
				}
			} else {
				$question = new \EventRegistrationQuestion();
				$question->container_guid = $event->guid;
				$question->owner_guid = $event->guid;
				$question->access_id = $event->access_id;
			}
			
			$question->title = $questiontext;
			
			if ($question->save()) {
				$question->fieldtype = $fieldtype;
				$question->required = $required;
				$question->fieldoptions = $fieldoptions;
				$question->order = $order;
			
				$question->addRelationship($event->guid, 'event_registrationquestion_relation');
				
				$order++;
				
				$saved_questions[] = $question->guid;
			}
		}
	}
	
	$current_questions = $event->getRegistrationFormQuestions();
	foreach ($current_questions as $current_question) {
		if (in_array($current_question->guid, $saved_questions)) {
			continue;
		}
		
		$current_question->delete();
	}
});

// added because we need an update event
$event->save();

// remove sticky form entries
elgg_clear_sticky_form('event');

$forward_url = $event->getURL();
if (!$has_days && $event->with_program) {
	// need to create a program
	$forward_url = elgg_generate_url('edit:object:event:program', ['guid' => $event->guid]);
}

return elgg_ok_response('', elgg_echo('event_manager:action:event:edit:ok'), $forward_url);
