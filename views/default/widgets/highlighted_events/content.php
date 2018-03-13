<?php

$widget = elgg_extract('entity', $vars);
$event_guids = $widget->event_guids;
$show_past_events = (bool) $widget->show_past_events;

if (empty($event_guids)) {
	echo elgg_echo('notfound');
	return;
}

$events = [];

$entities = elgg_get_entities([
	'type' => 'object',
	'subtype' => 'event',
	'guids' => $event_guids,
	'preload_owners' => true,
	'preload_containers' => true,
]);

foreach ($entities as $event) {
	if (!$show_past_events) {
		if ($event->getEndTimestamp() < time()) {
			continue;
		}
	}
	
	$events[$event->guid] = $event;
}

$sorted_entities = [];
foreach ($event_guids as $guid) {
	$entity = elgg_extract($guid, $events);
	if (!$entity) {
		continue;
	}
	
	$sorted_entities[] = $entity;
}

if (empty($sorted_entities)) {
	echo elgg_echo('notfound');
} else {
	echo elgg_view_entity_list($sorted_entities, [
		'full_view' => false,
		'no_results' => elgg_echo('notfound'),
	]);
}
