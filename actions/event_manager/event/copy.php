<?php

$guid = (int) get_input('guid');
if (empty($guid)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

$old_event = get_entity($guid);
if (!$old_event instanceof \Event || !$old_event->canEdit()) {
	return elgg_error_response(elgg_echo('actionunauthorized'));
}

$new_owner_guid = elgg_get_logged_in_user_guid();
$new_container_guid = $old_event->getContainerGUID();
if ($old_event->getContainerEntity() instanceof \ElggUser) {
	$new_container_guid = elgg_get_logged_in_user_guid();
}

$new_event = clone $old_event;
$new_event->time_created = null;
$new_event->time_updated = null;
$new_event->last_action = null;
$new_event->owner_guid = $new_owner_guid;
$new_event->container_guid = $new_container_guid;

$new_event->title = get_input('title', elgg_echo('event_manager:entity:copy', [$old_event->title]));
$new_event->access_id = get_input('access_id', $new_event->access_id);

$event_start = (int) get_input('event_start');
if ($event_start) {
	$event_start += (int) get_input('start_time');
	$new_event->event_start = $event_start;
}

$event_end = (int) get_input('event_end');
if ($event_end) {
	$event_end += (int) get_input('end_time');
	$new_event->event_end = $event_end;
}

if (!$new_event->save()) {
	return elgg_error_response(elgg_echo('unknown_error'));
}

foreach ($old_event->getEventDays() as $day) {
	$new_day = clone $day;
	$new_day->time_created = null;
	$new_day->time_updated = null;
	$new_day->last_action = null;
	$new_day->container_guid = $new_event->guid;
	$new_day->owner_guid = $new_event->guid;
	
	$new_day->save();
	$new_day->addRelationship($new_event->guid, 'event_day_relation');

	foreach ($day->getEventSlots() as $slot) {
		$new_slot = clone $slot;
		$new_slot->time_created = null;
		$new_slot->time_updated = null;
		$new_slot->last_action = null;
		$new_slot->container_guid = $new_day->getContainerGUID();
		$new_slot->owner_guid = $new_day->getOwnerGUID();
		$new_slot->save();
		$new_slot->addRelationship($new_day->guid, 'event_day_slot_relation');
	}
}

foreach ($old_event->getRegistrationFormQuestions() as $question) {
	$new_question = clone $question;
	$new_question->time_created = null;
	$new_question->time_updated = null;
	$new_question->last_action = null;
	$new_question->container_guid = $new_event->guid;
	$new_question->owner_guid = $new_event->guid;
	$new_question->save();
	$new_question->addRelationship($new_event->guid, 'event_registrationquestion_relation');
}

// copy all event files
$dir = new \Elgg\EntityDirLocator($old_event->guid);
$source = elgg_get_data_path() . $dir;
if (is_dir($source)) {
	$dir = new \Elgg\EntityDirLocator($new_event->guid);
	$dest = elgg_get_data_path() . $dir;
	
	// create new event data structure bucket
	mkdir($dest, 0755, true);
	
	$iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);
	foreach ($iterator as $item) {
		if ($item->isDir()) {
			mkdir($dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName(), 0755, true);
		} else {
			copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
		}
	}
}

return elgg_ok_response('', elgg_echo('event_manager:action:event:edit:ok'), $new_event->getURL());
