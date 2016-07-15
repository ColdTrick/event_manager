<?php
/**
 * Form fields for event settings
 */

$notify_onsignup = elgg_view('input/checkboxes', [
	'name' => 'notify_onsignup',
	'value' => $vars['notify_onsignup'],
	'options' => [
		elgg_echo('event_manager:edit:form:notify_onsignup') => '1',
	],
]);

$show_attendees = elgg_view('input/checkboxes', [
	'name' => 'show_attendees',
	'value' => $vars['show_attendees'],
	'options' => [
		elgg_echo('event_manager:edit:form:show_attendees') => '1',
	],
]);

echo elgg_view('elements/forms/field', [
	'label' => elgg_view('elements/forms/label', [
		'label' => elgg_echo('event_manager:edit:form:options'),
	]),
	'input' => $notify_onsignup . $show_attendees,
	'class' => 'event-manager-forms-label-normal',
]);

echo elgg_view_input('longtext', [
	'label' => elgg_echo('event_manager:edit:form:registration_completed'),
	'name' => 'registration_completed',
	'value' => $vars['registration_completed'],
	'help' => elgg_echo('event_manager:edit:form:registration_completed:description'),
	'field_class' => 'event-manager-forms-label-inline',
]);
