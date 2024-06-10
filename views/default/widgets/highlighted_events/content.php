<?php

/* @var $widget \ElggWidget */
$widget = elgg_extract('entity', $vars);
$event_guids = $widget->event_guids;
$show_past_events = (bool) $widget->show_past_events;

if (empty($event_guids)) {
	echo elgg_echo('event_manager:list:noresults');
	return;
}

$event_guids = (array) $event_guids;

$events = [];

$entities = elgg_get_entities([
	'type' => 'object',
	'subtype' => \Event::SUBTYPE,
	'guids' => $event_guids,
	'limit' => false,
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

echo elgg_view_entity_list($sorted_entities, [
	'full_view' => false,
	'pagination' => false,
	'limit' => false,
	'no_results' => elgg_echo('event_manager:list:noresults'),
]);
