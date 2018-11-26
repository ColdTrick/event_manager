<?php

$events_options = [
	'limit' => false,
	'past_events' => true,
	'metadata_name_value_pairs' => [],
];

$start = get_input('start');
$end = get_input('end');

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

$events = elgg_get_entities(event_manager_get_default_list_options($events_options));

$result = [];

foreach ($events as $event) {
	
	$start = $event->getStartDate();
	$end = $event->getEndDate('c');
	
	$all_day = $event->isMultiDayEvent();
	if ($all_day) {
		// needed for fullcalendar behaviour of allday events
		$end = date('c', strtotime($end . ' +1 day'));
	}
	
	$event_result = [
		'title' => $event->getDisplayName(),
		'start' => $start,
		'end' => $end,
		'allDay' => $all_day,
		'url' => $event->getURL(),
	];
	
	$result[] = $event_result;
}

header('Content-type: application/json');

echo json_encode($result);
