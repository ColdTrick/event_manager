<?php

$parent_guid = (int) get_input('parent_guid');

elgg_entity_gatekeeper($parent_guid, 'object', Event::SUBTYPE);
$event = get_entity($parent_guid);

if (!$event->canEdit()) {
	register_error(elgg_echo('actionunauthorized'));
	forward(REFERER);
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
	register_error(elgg_echo('save:fail'));
	forward(REFERER);
}

$day->title = $title;
$day->description = $description;
$day->container_guid = $event->getGUID();
$day->owner_guid = $event->getGUID();
$day->access_id = $event->access_id;

if (!$day->save()) {
	register_error(elgg_echo('save:fail'));
	forward(REFERER);
}

$day->date = $date;

$day->addRelationship($event->getGUID(), 'event_day_relation');

$content_title = $day->description;
if (empty($content_title)) {
	$content_title = event_manager_format_date($day->date);
}

if (!$edit) {
	$content_title = '<li><a rel="day_' . $day->getGUID() . '" href="javascript:void(0);">' . $content_title . '</a></li>';
}

$result = [
	'guid' => $day->getGUID(),
	'edit' => $edit,
	'content_title' => $content_title,
	'content_body' => elgg_view('event_manager/program/elements/day', [
		'entity' => $day,
		'details_only' => $edit
	])
];

echo json_encode($result);
