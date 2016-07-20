<?php

$events_options = [
	'limit' => false,
	'past_events' => true,
];

$start = get_input('start');
$end = get_input('end');

if ($start) {
	$events_options['event_start'] = strtotime($start);
}

if ($end) {
	$events_options['event_end'] = strtotime($end);
}

$container_guid = (int) get_input('container_guid');
if ($container_guid) {
	$events_options['container_guid'] = $container_guid;
}

$events = event_manager_search_events($events_options);

$result = [];

foreach ($events['entities'] as $event) {
	
	$start = $event->getStartDate();
	$end = $event->getEndDate();
	
	$event_result = [
		'title' => $event->title,
		'start' => $start,
		'end' => $end,
		'allDay' => $event->isMultiDayEvent(),
		'url' => $event->getURL(),
	];
	
	$result[] = $event_result;
}

header('Content-type: application/json');

echo json_encode($result);
