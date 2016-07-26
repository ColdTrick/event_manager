<?php

$widget = elgg_extract('entity', $vars);

$event_guids = $widget->event_guids;

echo elgg_view_input('objectpicker',[
	'values' => $event_guids,
	'name' => 'params[event_guids]',
	'subtype' => 'event',
	'label' => elgg_echo('event_manager:widgets:highlighted_events:edit:event_guids'),
]);