<?php

elgg_require_js('event_manager/edit_event');

$event = elgg_extract('entity', $vars);
$fields = event_manager_prepare_form_vars($event);
$vars = array_merge($vars, $fields);

$hidden_inputs = ['latitude', 'longitude', 'guid', 'container_guid'];
foreach ($hidden_inputs as $hidden) {
	echo elgg_view_input('hidden', [
		'name' => $hidden,
		'value' => $fields[$hidden],
	]);
}

echo elgg_view('forms/event_manager/event/module', [
	'body' => elgg_view('forms/event_manager/event/tabs/general', $vars),
]);

$sections = ['profile', 'location', 'registration', 'extra'];
foreach ($sections as $section) {
	echo elgg_view('forms/event_manager/event/module', [
		'title' => elgg_echo("event_manager:edit:form:tabs:{$section}"),
		'class' => 'event-tab',
		'id' => "event-manager-forms-event-edit-{$section}",
		'body' => elgg_view("forms/event_manager/event/tabs/{$section}", $vars),
	]);
}

$footer = elgg_view_input('submit', ['value' => elgg_echo('save')]);
echo elgg_format_element('div', ['class' => 'elgg-foot mtl'], $footer);
