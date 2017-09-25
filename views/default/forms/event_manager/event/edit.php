<?php

elgg_require_js('event_manager/edit_event');

$event = elgg_extract('entity', $vars);
$fields = event_manager_prepare_form_vars($event);
$vars = array_merge($vars, $fields);

$hidden_inputs = ['latitude', 'longitude', 'guid', 'container_guid'];
foreach ($hidden_inputs as $hidden) {
	echo elgg_view_field([
		'#type' => 'hidden',
		'name' => $hidden,
		'value' => $fields[$hidden],
	]);
}

echo elgg_view('forms/event_manager/event/module', [
	'body' => elgg_view('forms/event_manager/event/tabs/general', $vars),
]);

$sections = ['profile', 'location', 'contact', 'registration', 'questions'];
foreach ($sections as $section) {
	echo elgg_view('forms/event_manager/event/module', [
		'section' => $section,
		'title' => elgg_echo("event_manager:edit:form:tabs:{$section}"),
		'id' => "event-manager-forms-event-edit-{$section}",
		'body' => elgg_view("forms/event_manager/event/tabs/{$section}", $vars),
		'body_vars' => $vars,
	]);
}

$footer = elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_echo('save'),
]);
elgg_set_form_footer($footer);
