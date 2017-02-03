<?php

$event = elgg_extract('entity', $vars);
if (empty($event)) {
	return;
}

echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'guid', 
	'value' => $event->guid,
]);

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('title'),
	'name' => 'title', 
	'required' => true
]);

echo elgg_view_field([
	'#type' => 'file',
	'#label' => elgg_echo('event_manager:edit:form:file'),
	'name' => 'file', 
	'required' => true,
]);

$footer = elgg_view('input/submit', ['value' => elgg_echo('upload')]);
elgg_set_form_footer($footer);
