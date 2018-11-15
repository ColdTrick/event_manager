<?php

$parent_guid = (int) get_input('parent_guid');

elgg_entity_gatekeeper($parent_guid, 'object', Event::SUBTYPE);
$event = get_entity($parent_guid);

if (!$event->canEdit()) {
	return elgg_error_response(elgg_echo('actionunauthorized'));
}

$guid = (int) get_input('guid');
$title = get_input('title');
$description = get_input('description');
$date = (int) get_input('date');

$edit = false;

if ($guid && $day = get_entity($guid)) {
	// edit existing
	if (!($day instanceof \ColdTrick\EventManager\Event\Day)) {
		unset($day);
	}
	$edit = true;
} else {
	// create new
	$day = new \ColdTrick\EventManager\Event\Day();
}

if (empty($day) || empty($date)) {
	return elgg_error_response(elgg_echo('save:fail'));
}

$day->title = $title;
$day->description = $description;
$day->container_guid = $event->guid;
$day->owner_guid = $event->guid;
$day->access_id = $event->access_id;

if (!$day->save()) {
	return elgg_error_response(elgg_echo('save:fail'));
}

$day->date = $date;

$day->addRelationship($event->guid, 'event_day_relation');

$content_title = $day->description;
if (empty($content_title)) {
	$content_title = event_manager_format_date($day->date);
}

if (!$edit) {
	$content_title = '<li><a rel="day_' . $day->guid . '" href="javascript:void(0);">' . $content_title . '</a></li>';
}

$result = [
	'guid' => $day->guid,
	'edit' => $edit,
	'content_title' => $content_title,
	'content_body' => elgg_view('event_manager/program/elements/day', [
		'entity' => $day,
		'details_only' => $edit
	]),
];

return elgg_ok_response($result);
