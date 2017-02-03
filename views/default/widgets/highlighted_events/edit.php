<?php

$widget = elgg_extract('entity', $vars);

echo elgg_view_field([
	'#type' => 'objectpicker',
	'#label' => elgg_echo('event_manager:widgets:highlighted_events:edit:event_guids'),
	'#help' => elgg_echo('event_manager:widgets:highlighted_events:description'),
	'values' => $widget->event_guids,
	'name' => 'params[event_guids]',
	'subtype' => 'event',
	'sortable' => true,
]);

echo elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('event_manager:widgets:highlighted_events:edit:show_past_events'),
	'name' => 'params[show_past_events]',
	'value' => 1,
	'checked' => (bool) $widget->show_past_events,
]);