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
	$title_anchor = elgg_view('output/url', [
		'href' => "#event_manager_event_view_program-{$day->guid}",
		'text' => $content_title,
		'rel' => "day_{$day->guid}",
		'data-target' => "#event_manager_event_view_program-{$day->guid}",
		'class' => [
			'elgg-menu-content',
		],
	]);
	$content_title = elgg_format_element('li', [
		'data-menu-item' => "event_manager_event_view_program-tab-{$day->guid}",
		'class' => [
			"elgg-menu-item-event-manager-event-view-program-tab-{$day->guid}",
			'elgg-components-tab',
		],
	], $title_anchor);
}

$content_body = elgg_view('event_manager/program/elements/day', [
	'entity' => $day,
	'details_only' => $edit,
]);

if (!$edit) {
	$content_body = elgg_format_element('div', [
		'class' => 'elgg-content',
		'id' => "event_manager_event_view_program-{$day->guid}",
	], $content_body);
}
	
$result = [
	'guid' => $day->guid,
	'edit' => $edit,
	'content_title' => $content_title,
	'content_body' => $content_body,
];

return elgg_ok_response($result);
