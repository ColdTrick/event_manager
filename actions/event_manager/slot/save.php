<?php

$parent_guid = (int) get_input('parent_guid');

$day = elgg_call(ELGG_IGNORE_ACCESS, function() use ($parent_guid) {
	// days are unavailable if event is private
	return get_entity($parent_guid);
});

if (!$day instanceof \ColdTrick\EventManager\Event\Day) {
	return elgg_error_response(elgg_echo('event_manager:action:slot:day_not_found'));
}

if (!$day->canEdit()) {
	return elgg_error_response(elgg_echo('actionunauthorized'));
}

$edit = false;

$guid = (int) get_input('guid');
$title = get_input('title');
$description = get_input('description');

$start_time_ts = (int) get_input('start_time');

$minutes = $start_time_ts % 3600;
$hours = $start_time_ts - $minutes;

// special handling of time for BC reasons
$start_time = mktime($hours / 3600, $minutes / 60, 1, 0, 0, 0);

$end_time_ts = (int) get_input('end_time');
$minutes = $end_time_ts % 3600;
$hours = $end_time_ts - $minutes;
$end_time = mktime($hours / 3600, $minutes / 60, 1, 0, 0, 0);

$location = get_input('location');
$max_attendees = (int) get_input('max_attendees');

$slot_set = get_input('slot_set');

if (empty($title) || empty($start_time) || empty($end_time)) {
	return elgg_error_response(elgg_echo('event_manager:action:slot:missing_fields'));
}

if ($guid) {
	// edit existing
	$slot = elgg_call(ELGG_IGNORE_ACCESS, function() use ($guid) {
		// slots are unavailable if event is private
		return get_entity($guid);
	});

	if (!$slot instanceof \ColdTrick\EventManager\Event\Slot) {
		return elgg_error_response(elgg_echo('event_manager:action:slot:not_found'));
	}

	$edit = true;
} else {
	// create new
	$slot = new \ColdTrick\EventManager\Event\Slot();
}

$slot->title = $title;
$slot->description = $description;
$slot->container_guid = $day->container_guid;
$slot->owner_guid = $day->owner_guid;
$slot->access_id = $day->access_id;
$slot->start_time = $start_time;
$slot->end_time = $end_time;
$slot->location = $location;
$slot->max_attendees = $max_attendees ?: null;

if (!$slot->save()) {
	return elgg_error_response(elgg_echo('event_manager:action:slot:cannot_save'));
}

if (!empty($slot_set)) {
	$slot->slot_set = $slot_set;
}

if (!$edit) {
	$slot->addRelationship($day->guid, 'event_day_slot_relation');
}

$result = [
	'edit' => $edit,
	'guid' => $slot->guid,
	'parent_guid' => $parent_guid,
	'content' => elgg_view('event_manager/program/elements/slot', ['entity' => $slot]),
];

return elgg_ok_response($result, elgg_echo('event_manager:action:slot:saved'));
