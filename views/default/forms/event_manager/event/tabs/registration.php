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

$with_program = elgg_view('input/checkboxes', [
	'name' => 'with_program',
	'id' => 'with_program',
	'value' => $vars['with_program'],
	'options' => [elgg_echo('event_manager:edit:form:with_program') => '1'],
]);

$registration_needed = elgg_view('input/checkboxes', [
	'name' => 'registration_needed',
	'value' => $vars['registration_needed'],
	'options' => [elgg_echo('event_manager:edit:form:registration_needed') => '1'],
]);

$register_nologin = '';
if (!elgg_get_config('walled_garden')) {
	$register_nologin = elgg_view('input/checkboxes', [
		'name' => 'register_nologin',
		'value' => $vars['register_nologin'],
		'options' => [elgg_echo('event_manager:edit:form:register_nologin') => '1'],
	]);
}

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
		'label' => elgg_echo('event_manager:edit:form:registration_options'),
	]),
	'input' => $with_program . $registration_needed  . $register_nologin . $notify_onsignup . $show_attendees,
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
