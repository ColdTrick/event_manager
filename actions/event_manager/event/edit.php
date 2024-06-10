<?php

// start a new sticky form session in case of failure
elgg_make_sticky_form('event');

$title = get_input('title');

$event_start_midnight = \Elgg\Values::normalizeTime(gmdate('c', (int) get_input('event_start')));
$event_start_midnight->setTime(0, 0, 0);
$event_start = $event_start_midnight->getTimestamp();

$start_time = (int) get_input('start_time');

$event_end = \Elgg\Values::normalizeTime(gmdate('c', (int) get_input('event_end')));
$event_end->setTime(0, 0, 0);
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

$entity = get_entity((int) get_input('guid'));
$event_created = false;
if ($entity instanceof \Event) {
	$event = $entity;
} else {
	$event_created = true;
	$event = new \Event();
}

$announcement_period = (int) get_input('announcement_period');
if ($announcement_period < 1) {
	$notification_queued_ts = time();
} else {
	$notification_queued_ts = $event_start_midnight->modify("-{$announcement_period} weeks")->getTimestamp();
	if ($notification_queued_ts <= time()) {
		$notification_queued_ts = time();
	}
}

$event->title = $title;
$event->description = get_input('description');
$event->container_guid = (int) get_input('container_guid');
$event->access_id = $access_id;
if (empty($event->notification_sent_ts) && (!empty($event->notification_queued_ts) || $event_created)) {
	// only set if notifications are not sent
	// only set for new events or if previously saved with a notification queued (to differentiate with event with the new notification logic)
	$event->announcement_period = $announcement_period;
	$event->notification_queued_ts = $notification_queued_ts;
}

if (empty($event->notification_sent_ts) && !$event_created && $event->notification_queued_ts <= time()) {
	// event updated but notification needs to be sent immediately
	elgg_enqueue_notification_event('create', $event);
	
	if ($access_id !== ACCESS_PRIVATE) {
		// prevent double enqueueing of notifications if using advanced_notifications plugin
		unset($event->advanced_notifications_delayed_action);
	}
}

$event->save();

$event->location = get_input('location');
$event->setLatLong((float) get_input('latitude'), (float) get_input('longitude'));

$event->tags = elgg_string_to_array((string) get_input('tags'));

if ($event_created) {
	elgg_create_river_item([
		'action_type' => 'create',
		'subject_guid' => elgg_get_logged_in_user_guid(),
		'object_guid' => $event->guid,
	]);
}

$event->setMaxAttendees((int) get_input('max_attendees'));

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

if (get_input('header_remove')) {
	$event->deleteIcon('header');
} else {
	$event->saveIconFromUploadedFile('header', 'header');
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

elgg_clear_sticky_form('event');

$forward_url = $event->getURL();
if (!$has_days && $event->with_program) {
	// need to create a program
	$forward_url = elgg_generate_url('edit:object:event:program', ['guid' => $event->guid]);
}

return elgg_ok_response('', elgg_echo('event_manager:action:event:edit:ok'), $forward_url);
