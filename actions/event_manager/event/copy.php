<?php

use Elgg\Values;

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

unset($new_event->notification_sent_ts);

$announcement_period = (int) get_input('announcement_period');
if ($announcement_period < 1) {
	$notification_queued_ts = time();
} else {
	$notification_queued_ts = Values::normalizeTime($event_start)->setTime(0, 0, 0)->modify("-{$announcement_period} weeks")->getTimestamp();
	if ($notification_queued_ts <= time()) {
		$notification_queued_ts = time();
	}
}

if (!empty($notification_queued_ts)) {
	// only set if notifications are not sent
	// only set for new events or if previously saved with a notification queued (to differentiate with event with the new notification logic)
	$new_event->announcement_period = $announcement_period;
	$new_event->notification_queued_ts = $notification_queued_ts;
} else {
	unset($new_event->announcement_period);
	unset($new_event->notification_queued_ts);
}

if (!$new_event->save()) {
	return elgg_error_response(elgg_echo('unknown_error'));
}

foreach ($old_event->getEventDays() as $day) {
	$new_day = clone $day;
	$new_day->container_guid = $new_event->guid;
	$new_day->owner_guid = $new_event->guid;
	
	$new_day->save();
	$new_day->addRelationship($new_event->guid, 'event_day_relation');

	foreach ($day->getEventSlots() as $slot) {
		$new_slot = clone $slot;
		$new_slot->container_guid = $new_day->getContainerGUID();
		$new_slot->owner_guid = $new_day->getOwnerGUID();
		$new_slot->save();
		$new_slot->addRelationship($new_day->guid, 'event_day_slot_relation');
	}
}

foreach ($old_event->getRegistrationFormQuestions() as $question) {
	$new_question = clone $question;
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
