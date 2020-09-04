<?php

$events_options = [
	'limit' => 999, // not wise to leave unlimited
	'type' => 'object',
	'subtype' => 'event',
	'metadata_name_value_pairs' => [],
];

$start = get_input('start');
$end = get_input('end');

if (empty($start) && empty($end)) {
	echo json_encode([]);
	return;
}

if ($start) {
	$events_options['metadata_name_value_pairs'][] = [
		'name' => 'event_end',
		'value' => strtotime($start),
		'operand' => '>='
	];
}

if ($end) {
	$events_options['metadata_name_value_pairs'][] = [
		'name' => 'event_start',
		'value' => strtotime($end),
		'operand' => '<='
	];
}

$container_guid = (int) get_input('container_guid');
if ($container_guid) {
	$events_options['container_guid'] = $container_guid;
}

$events = elgg_get_entities($events_options);

$result = [];

/* @var $event \Event */
foreach ($events as $event) {
	
	$start = $event->getStartDate();
	$end = $event->getEndDate('c');
	
	$all_day = $event->isMultiDayEvent();
	if ($all_day) {
		// needed for fullcalendar behaviour of allday events
		$end = date('c', strtotime($end . ' +1 day'));
	}
	
	$classes = [];
	if ($event->owner_guid === elgg_get_logged_in_user_guid()) {
		$classes[] = 'event-manager-calandar-owner';
	} elseif ($event->getRelationshipByUser()) {
		$classes[] = 'event-manager-calandar-attending';
	}
	
	$event_result = [
		'title' => $event->getDisplayName(),
		'start' => $start,
		'end' => $end,
		'allDay' => $all_day,
		'url' => $event->getURL(),
		'className' => $classes,
	];
	
	$result[] = $event_result;
}

echo json_encode($result);
