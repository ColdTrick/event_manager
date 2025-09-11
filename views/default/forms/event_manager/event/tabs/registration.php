<?php
/**
 * Form fields for entering registration settings
 */

$event = elgg_extract('entity', $vars);

echo elgg_view_field([
	'#type' => 'fieldset',
	'align' => 'horizontal',
	'fields' => [
		[
			'#type' => 'text',
			'#label' => elgg_echo('event_manager:edit:form:fee'),
			'#help' => elgg_echo('event_manager:edit:form:fee:help'),
			'name' => 'fee',
			'value' => $vars['fee'],
		],
		[
			'#type' => 'text',
			'#class' => 'elgg-field-stretch',
			'#label' => elgg_echo('event_manager:edit:form:fee_details'),
			'#help' => elgg_echo('event_manager:edit:form:fee_details:help'),
			'name' => 'fee_details',
			'value' => $vars['fee_details'],
		],
	],
]);

echo elgg_view_field([
	'#type' => 'fieldset',
	'align' => 'horizontal',
	'class' => 'event-manager-align-bottom',
	'fields' => [
		[
			'#type' => 'number',
			'#class' => 'elgg-field-stretch',
			'#label' => elgg_echo('event_manager:edit:form:max_attendees'),
			'#help' => elgg_echo('event_manager:edit:form:max_attendees:help'),
			'name' => 'max_attendees',
			'value' => $vars['max_attendees'],
			'min' => 0,
		],
		[
			'#type' => 'checkbox',
			'#label' => elgg_echo('event_manager:edit:form:waiting_list'),
			'#class' => (empty($vars['max_attendees']) && empty($vars['waiting_list_enabled'])) ? 'hidden' : null,
			'name' => 'waiting_list_enabled',
			'value' => 1,
			'checked' => (bool) $vars['waiting_list_enabled'],
		],
	],
]);

$fields = [
	[
		'#type' => 'switch',
		'#label' => elgg_echo('event_manager:edit:form:with_program'),
		'name' => 'with_program',
		'id' => 'with_program',
		'value' => $vars['with_program'],
	],
	[
		'#type' => 'switch',
		'#label' => elgg_echo('event_manager:edit:form:registration_needed'),
		'name' => 'registration_needed',
		'value' => $vars['registration_needed'],
	],
	[
		'#type' => 'switch',
		'#label' => elgg_echo('event_manager:edit:form:show_attendees'),
		'name' => 'show_attendees',
		'value' => $vars['show_attendees'],
	],
	[
		'#type' => 'switch',
		'#label' => elgg_echo('event_manager:edit:form:notify_onsignup'),
		'name' => 'notify_onsignup',
		'value' => $vars['notify_onsignup'],
	],
	[
		'#type' => 'switch',
		'#label' => elgg_echo('event_manager:edit:form:notify_onsignup_contact'),
		'#class' => 'pll',
		'name' => 'notify_onsignup_contact',
		'value' => (bool) $vars['notify_onsignup_contact'],
	],
	[
		'#type' => 'switch',
		'#label' => elgg_echo('event_manager:edit:form:notify_onsignup_organizer'),
		'#class' => 'pll',
		'name' => 'notify_onsignup_organizer',
		'value' => $vars['notify_onsignup_organizer'],
	],
];

if (!elgg_get_config('walled_garden')) {
	array_unshift($fields, [
		'#type' => 'switch',
		'#label' => elgg_echo('event_manager:edit:form:register_nologin'),
		'name' => 'register_nologin',
		'value' => $vars['register_nologin'],
	]);
}

echo elgg_view_field([
	'#type' => 'fieldset',
	'legend' => elgg_echo('event_manager:edit:form:registration_options'),
	'fields' => $fields,
]);

echo elgg_view_field([
	'#type' => 'fieldset',
	'align' => 'horizontal',
	'class' => 'event-manager-align-bottom',
	'fields' => [
		[
			'#type' => 'date',
			'#class' => 'elgg-field-stretch',
			'#label' => elgg_echo('event_manager:edit:form:endregistration_day'),
			'#help' => elgg_echo('event_manager:edit:form:endregistration_day:help'),
			'name' => 'endregistration_day',
			'id' => 'endregistration_day',
			'value' => $vars['endregistration_day'],
		],
		[
			'#type' => 'checkbox',
			'#label' => elgg_echo('event_manager:edit:form:registration_ended'),
			'name' => 'registration_ended',
			'value' => 1,
			'checked' => (bool) $vars['registration_ended'],
		],
	],
]);

echo elgg_view_field([
	'#type' => 'longtext',
	'#label' => elgg_echo('event_manager:edit:form:registration_completed'),
	'#help' => elgg_echo('event_manager:edit:form:registration_completed:description'),
	'name' => 'registration_completed',
	'value' => $vars['registration_completed'],
]);
