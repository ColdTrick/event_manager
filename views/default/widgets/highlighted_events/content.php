<?php

$widget = elgg_extract('entity', $vars);
$event_guids = $widget->event_guids;

if (empty($event_guids)) {
	echo elgg_echo('notfound');
	return;
}

foreach ($event_guids as $event_guid) {
	$event = get_entity($event_guid);
	if (!($event instanceof \Event)) {
		continue;
	}
	
	echo elgg_view_entity($event, ['full_view' => false]);
}
