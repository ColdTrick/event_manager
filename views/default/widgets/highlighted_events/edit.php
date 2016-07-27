<?php

$widget = elgg_extract('entity', $vars);

$event_guids = $widget->event_guids;

echo elgg_view_input('objectpicker',[
	'values' => $event_guids,
	'name' => 'params[event_guids]',
	'subtype' => 'event',
	'label' => elgg_echo('event_manager:widgets:highlighted_events:edit:event_guids'),
	'help' => elgg_echo('event_manager:widgets:highlighted_events:description'),
]);

echo elgg_view('input/checkbox', [
	'label' => elgg_echo('event_manager:widgets:highlighted_events:edit:show_past_events'),
	'name' => 'params[show_past_events]',
	'value' => 1,
	'checked' => (bool) $widget->show_past_events,
]);