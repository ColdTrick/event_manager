<?php
/**
 * Form fields for entering registration settings
 */

$event = elgg_extract('entity', $vars);

echo '<div class="event-manager-event-edit-level">';
echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('event_manager:edit:form:fee'),
	'#help' => elgg_echo('event_manager:edit:form:fee:help'),
	'name' => 'fee',
	'value' => $vars['fee'],
]);

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('event_manager:edit:form:fee_details'),
	'#help' => elgg_echo('event_manager:edit:form:fee_details:help'),
	'#class' => empty($vars['fee']) ? 'hidden' : null,
	'name' => 'fee_details',
	'value' => $vars['fee_details'],
]);
echo '</div>';

echo '<div class="event-manager-event-edit-level">';
echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('event_manager:edit:form:max_attendees'),
	'#help' => elgg_echo('event_manager:edit:form:max_attendees:help'),
	'name' => 'max_attendees',
	'value' => $vars['max_attendees'],
]);

echo elgg_view_field([
	'#type' => 'checkboxes',
	'#class' => (empty($vars['max_attendees']) && empty($vars['waiting_list_enabled'])) ? 'hidden' : null,
	'name' => 'waiting_list_enabled',
	'value' => $vars['waiting_list_enabled'],
	'options' => [elgg_echo('event_manager:edit:form:waiting_list') => '1'],
	'class' => 'mts',
]);
echo '</div>';

$fields = [
	[
		'#type' => 'checkbox',
		'#label' => elgg_echo('event_manager:edit:form:with_program'),
		'name' => 'with_program',
		'id' => 'with_program',
		'checked' => (bool) $vars['with_program'],
		'switch' => true,
		'default' => 0,
		'value' => 1,
	],
	[
		'#type' => 'checkbox',
		'#label' => elgg_echo('event_manager:edit:form:registration_needed'),
		'name' => 'registration_needed',
		'checked' => (bool) $vars['registration_needed'],
		'switch' => true,
		'default' => 0,
		'value' => 1,
	],
	[
		'#type' => 'checkbox',
		'#label' => elgg_echo('event_manager:edit:form:show_attendees'),
		'name' => 'show_attendees',
		'checked' => (bool) $vars['show_attendees'],
		'switch' => true,
		'default' => 0,
		'value' => 1,
	],
	[
		'#type' => 'checkbox',
		'#label' => elgg_echo('event_manager:edit:form:notify_onsignup'),
		'name' => 'notify_onsignup',
		'checked' => (bool) $vars['notify_onsignup'],
		'switch' => true,
		'default' => 0,
		'value' => 1,
	],
	[
		'#type' => 'checkbox',
		'#label' => elgg_echo('event_manager:edit:form:notify_onsignup_contact'),
		'#class' => 'mll',
		'name' => 'notify_onsignup_contact',
		'checked' => (bool) $vars['notify_onsignup_contact'],
		'switch' => true,
		'default' => 0,
		'value' => 1,
	],
	[
		'#type' => 'checkbox',
		'#label' => elgg_echo('event_manager:edit:form:notify_onsignup_organizer'),
		'#class' => 'mll',
		'name' => 'notify_onsignup_organizer',
		'checked' => (bool) $vars['notify_onsignup_organizer'],
		'switch' => true,
		'default' => 0,
		'value' => 1,
	],
];

if (!elgg_get_config('walled_garden')) {
	array_unshift($fields, [
		'#type' => 'checkbox',
		'#label' => elgg_echo('event_manager:edit:form:register_nologin'),
		'name' => 'register_nologin',
		'checked' => (bool) $vars['register_nologin'],
		'switch' => true,
		'default' => 0,
		'value' => 1,
	]);
}

echo elgg_view_field([
	'#type' => 'fieldset',
	'legend' => elgg_echo('event_manager:edit:form:registration_options'),
	'fields' => $fields,
]);

echo '<div class="event-manager-event-edit-level">';
echo elgg_view_field([
	'#type' => 'date',
	'#label' => elgg_echo('event_manager:edit:form:endregistration_day'),
	'#help' => elgg_echo('event_manager:edit:form:endregistration_day:help'),
	'name' => 'endregistration_day',
	'id' => 'endregistration_day',
	'value' => $vars['endregistration_day'],
]);

echo elgg_view_field([
	'#type' => 'checkboxes',
	'name' => 'registration_ended',
	'value' => $vars['registration_ended'],
	'options' => [elgg_echo('event_manager:edit:form:registration_ended') => '1'],
	'class' => 'mts',
]);
echo '</div>';

echo elgg_view_field([
	'#type' => 'longtext',
	'#label' => elgg_echo('event_manager:edit:form:registration_completed'),
	'#help' => elgg_echo('event_manager:edit:form:registration_completed:description'),
	'name' => 'registration_completed',
	'value' => $vars['registration_completed'],
]);
