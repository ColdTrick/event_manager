<?php

$widget = elgg_extract('entity', $vars);
$event_guids = $widget->event_guids;
$show_past_events = (bool) $widget->show_past_events;

if (empty($event_guids)) {
	echo elgg_echo('notfound');
	return;
}

$events = [];
foreach ($event_guids as $event_guid) {
	$event = get_entity($event_guid);
	if (!($event instanceof \Event)) {
		continue;
	}
	if (!$show_past_events) {
		if ($event->getEndTimestamp() < time()) {
			continue;
		}
	}
	
	$events[] = $event;
}

if (empty($events)) {
	echo elgg_echo('notfound');
} else {
	echo elgg_view_entity_list($events, [
		'full_view' => false,
		'no_results' => elgg_echo('notfound'),
	]);
}