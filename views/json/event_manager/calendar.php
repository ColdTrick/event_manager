<?php

$events_options = [
	'type' => 'object',
	'subtype' => \Event::SUBTYPE,
	'limit' => 999, // not wise to leave unlimited
	'batch' => true,
	'metadata_name_value_pairs' => [],
];

$start = get_input('start');
$end = get_input('end');
$guid = (int) get_input('guid');
$resource = get_input('resource');

if (empty($start) && empty($end)) {
	echo json_encode([]);
	return;
}

if (!empty($start)) {
	$events_options['metadata_name_value_pairs'][] = [
		'name' => 'event_end',
		'value' => strtotime($start),
		'operand' => '>='
	];
}

if (!empty($end)) {
	$events_options['metadata_name_value_pairs'][] = [
		'name' => 'event_start',
		'value' => strtotime($end),
		'operand' => '<='
	];
}

$entity = get_entity($guid);
if ($entity instanceof ElggGroup) {
	$events_options['container_guid'] = $entity->guid;
}

switch ($resource) {
	case 'owner':
		if (!$entity instanceof ElggUser) {
			echo json_encode([]);
			return;
		}
		
		$events_options['owner_guid'] = $entity->guid;
		break;
	case 'attending':
		if (!$entity instanceof ElggUser) {
			echo json_encode([]);
			return;
		}
		
		$events_options['relationship'] = EVENT_MANAGER_RELATION_ATTENDING;
		$events_options['relationship_guid'] = $entity->guid;
		$events_options['inverse_relationship'] = true;
		break;
}

// let others extend this
$params = [
	'resource' => $resource,
	'guid' => $guid,
	'start' => $start,
	'end' => $end,
];
$events_options = elgg_trigger_plugin_hook('calendar_data:options', 'event_manager', $params, $events_options);

// fetch data
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
		$classes[] = 'event-manager-calendar-owner';
	} elseif ($event->getRelationshipByUser()) {
		$classes[] = 'event-manager-calendar-attending';
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
