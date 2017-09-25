<?php

// start a new sticky form session in case of failure
elgg_make_sticky_form('event');

$title = get_input('title');

$event_start = (int) get_input('event_start');
$start_time_hours = (int) get_input('start_time_hours');
$start_time_minutes = (int) get_input('start_time_minutes');

$event_end = (int) get_input('event_end');
$end_time_hours = (int) get_input('end_time_hours');
$end_time_minutes = (int) get_input('end_time_minutes');

$endregistration_day = get_input('endregistration_day');

$access_id = (int) get_input('access_id');

if (empty($title) || empty($event_start) || empty($event_end)) {
	register_error(elgg_echo('event_manager:action:event:edit:error_fields'));
	forward(REFERER);
}

$event_end += $end_time_minutes * 60;
$event_end += $end_time_hours * 3600;


$event_start += $start_time_minutes * 60;
$event_start += $start_time_hours * 3600;

if ($event_end < $event_start) {
	register_error(elgg_echo('event_manager:action:event:edit:end_before_start'));
	forward(REFERER);
}

if (!empty($endregistration_day)) {
	$date_endregistration_day = explode('-', $endregistration_day);
	$endregistration_day = mktime(0, 0, 1, $date_endregistration_day[1], $date_endregistration_day[2], $date_endregistration_day[0]);
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

$event->setLocation(get_input('location'));
$event->setLatLong(get_input('latitude'), get_input('longitude'));

$event->tags = string_to_tag_array(get_input('tags'));

if ($event_created) {
	elgg_create_river_item([
		'view' => 'river/object/event/create',
		'action_type' => 'create',
		'subject_guid' => elgg_get_logged_in_user_guid(),
		'object_guid' => $event->getGUID(),
	]);
}

$event->setMaxAttendees(get_input('max_attendees'));
$event->setRegion(get_input('region'));
$event->setEventType(get_input('event_type'));

$event->event_start = $event_start;
$event->event_end = $event_end;

$event->with_program = get_input('with_program');
$event->endregistration_day = $endregistration_day;
$event->event_interested = 1;

$metadata_fields = [
	'shortdescription', 'comments_on', 'registration_ended', 'registration_needed', 'show_attendees',
	'notify_onsignup', 'waiting_list', 'venue', 'contact_details', 'website',
	'organizer', 'fee', 'fee_details', 'register_nologin', 'waiting_list_enabled', 'registration_completed',
	'organizer_guids', 'contact_guids',
];

foreach ($metadata_fields as $field) {
	$event->{$field} = get_input($field);
}

$has_days = $event->hasEventDays();
$event->generateInitialProgramData();

if (get_input('delete_current_icon')) {
	$event->deleteIcon();
} elseif ($uploaded_files = elgg_get_uploaded_files('icon')) {
	/* @var $uploaded_file \Symfony\Component\HttpFoundation\File\UploadedFile */
	$uploaded_file = $uploaded_files[0];
	if (stripos($uploaded_file->getMimeType(), 'image/') !== false) {
		$event->saveIconFromUploadedFile('icon');
	}
}

$ia = elgg_set_ignore_access(true);

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
		
			$question->addRelationship($event->getGUID(), 'event_registrationquestion_relation');
			
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

elgg_set_ignore_access($ia);

// added because we need an update event
$event->save();

// remove sticky form entries
elgg_clear_sticky_form('event');

system_message(elgg_echo('event_manager:action:event:edit:ok'));

if (!$has_days && $event->with_program) {
	// need to create a program
	forward("events/event/edit_program/{$event->getGUID()}");
}

forward($event->getURL());
