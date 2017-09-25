<?php

$guid = (int) get_input('guid');
elgg_entity_gatekeeper($guid, 'object', \Event::SUBTYPE);

$old_event = get_entity($guid);
if (!$old_event->canEdit()) {
	register_error(elgg_echo('actionunauthorized'));
	forward(REFERER);
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

$new_event->title = elgg_echo('event_manager:enity:copy', [$old_event->title]);

if (!$new_event->save()) {
	register_error(elgg_echo('unknown_error'));
	forward(REFERER);
}

foreach ($old_event->getEventDays() as $day) {
	$new_day = clone $day;
	$new_day->time_created = null;
	$new_day->time_updated = null;
	$new_day->last_action = null;
	$new_day->container_guid = $new_event->getGUID();
	$new_day->owner_guid = $new_event->getGUID();
	
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
	$new_question->container_guid = $new_event->getGUID();
	$new_question->owner_guid = $new_event->getGUID();
	$new_question->save();
	$new_question->addRelationship($new_event->guid, 'event_registrationquestion_relation');
}

// copy all event files
$dir = new \Elgg\EntityDirLocator($old_event->guid);
$source = elgg_get_data_path() . $dir;
mkdir($source);

$dir = new \Elgg\EntityDirLocator($new_event->guid);
$dest = elgg_get_data_path() . $dir;

mkdir($dest);

$iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);
foreach ($iterator as $item) {
	if ($item->isDir()) {
		mkdir($dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
	} else {
		copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
	}
}

system_message(elgg_echo('event_manager:action:event:edit:ok'));
forward($new_event->getURL());
