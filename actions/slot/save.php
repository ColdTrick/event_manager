<?php

$parent_guid = (int) get_input('parent_guid');

$day = get_entity($parent_guid);

if (!$day instanceof EventDay) {
	register_error(elgg_echo('event_manager:action:slot:day_not_found'));
	forward(REFERER);
}

if (!$day->canEdit()) {
	register_error(elgg_echo('actionunauthorized'));
	forward(REFERER);
}

$edit = false;

$guid = (int) get_input('guid');
$title = get_input('title');
$description = get_input('description');

$start_time_hours = get_input('start_time_hours');
$start_time_minutes = get_input('start_time_minutes');
$start_time = mktime($start_time_hours, $start_time_minutes, 1, 0, 0, 0);

$end_time_hours = get_input('end_time_hours');
$end_time_minutes = get_input('end_time_minutes');
$end_time = mktime($end_time_hours, $end_time_minutes, 1, 0, 0, 0);

$location = get_input('location');
$max_attendees = get_input('max_attendees');

$slot_set = get_input('slot_set');

if (empty($title) || empty($start_time) || empty($end_time)) {
	register_error(elgg_echo('event_manager:action:slot:missing_fields'));
	forward(REFERER);
}

if ($guid) {
	// edit existing
	$slot = get_entity($guid);

	if (!$slot instanceof EventSlot) {
		register_error(elgg_echo('event_manager:action:slot:not_found'));
		forward(REFERER);
	}

	$edit = true;
} else {
	// create new
	$slot = new EventSlot();
}

$slot->title = $title;
$slot->description = $description;
$slot->container_guid = $day->container_guid;
$slot->owner_guid = $day->owner_guid;
$slot->access_id = $day->access_id;
$slot->start_time = $start_time;
$slot->end_time = $end_time;
$slot->location = $location;
$slot->max_attendees = $max_attendees;

if (!$slot->save()) {
	register_error(elgg_echo('event_manager:action:slot:cannot_save'));
	forward(REFERER);
}

if (!empty($slot_set)) {
	$slot->slot_set = $slot_set;
}

if (!$edit) {
	$slot->addRelationship($day->getGUID(), 'event_day_slot_relation');
}

system_message(elgg_echo('event_manager:action:slot:saved'));

$result = array(
	'edit' => $edit,
	'guid' => $slot->guid,
	'parent_guid' => $parent_guid,
	'content' => elgg_view('event_manager/program/elements/slot', array('entity' => $slot)),
);

echo json_encode($result);
