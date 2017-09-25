<?php
/**
 * Form fields for entering registration settings
 */

$event = elgg_extract('entity', $vars);

echo '<div class="clearfix"><div class="elgg-col elgg-col-1of4">';
echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('event_manager:edit:form:fee'),
	'#help' => elgg_echo('event_manager:edit:form:fee:help'),
	'name' => 'fee',
	'value' => $vars['fee'],
]);
echo '</div>';
echo '<div class="elgg-col elgg-col-3of4">';
$field_class = 'pll';
if (empty($vars['fee'])) {
	$field_class .= ' hidden';
}
echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('event_manager:edit:form:fee_details'),
	'#help' => elgg_echo('event_manager:edit:form:fee_details:help'),
	'#class' => $field_class,
	'name' => 'fee_details',
	'value' => $vars['fee_details'],
]);
echo '</div></div>';

echo '<div class="clearfix"><div class="elgg-col elgg-col-1of4">';
echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('event_manager:edit:form:max_attendees'),
	'#help' => elgg_echo('event_manager:edit:form:max_attendees:help'),
	'name' => 'max_attendees',
	'value' => $vars['max_attendees'],
]);
echo '</div>';
echo '<div class="elgg-col elgg-col-3of4">';
$field_class = 'pll';
if (empty($vars['max_attendees']) && empty($vars['waiting_list_enabled'])) {
	$field_class .= ' hidden';
}
echo elgg_view_field([
	'#type' => 'checkboxes',
	'#label' => '&nbsp;',
	'#class' => $field_class,
	'name' => 'waiting_list_enabled',
	'value' => $vars['waiting_list_enabled'],
	'options' => [elgg_echo('event_manager:edit:form:waiting_list') => '1'],
	'class' => 'mts',
]);
echo '</div></div>';

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
	'class' => 'event-manager-forms-label-normal',
]);

echo '<div class="clearfix"><div class="elgg-col elgg-col-1of4">';
echo elgg_view_field([
	'#type' => 'date',
	'#label' => elgg_echo('event_manager:edit:form:endregistration_day'),
	'#help' => elgg_echo('event_manager:edit:form:endregistration_day:help'),
	'name' => 'endregistration_day',
	'id' => 'endregistration_day',
	'value' => $vars['endregistration_day'],
]);
echo '</div>';
echo '<div class="elgg-col elgg-col-3of4">';
echo elgg_view_field([
	'#type' => 'checkboxes',
	'#label' => '&nbsp;',
	'#class' => 'pll',
	'name' => 'registration_ended',
	'value' => $vars['registration_ended'],
	'options' => [elgg_echo('event_manager:edit:form:registration_ended') => '1'],
	'class' => 'mts',
]);
echo '</div></div>';

echo elgg_view('input/button', [
	'value' => elgg_echo('event_manager:edit:form:registration_completed:toggle'),
	'class' => 'elgg-button-action event-manager-edit-registration-completed',
	'rel' => 'toggle',
	'data-toggle-slide' => '0',
	'data-toggle-selector' => '.event-manager-edit-registration-completed',
]);
echo elgg_view_field([
	'#type' => 'longtext',
	'#label' => elgg_echo('event_manager:edit:form:registration_completed'),
	'#help' => elgg_echo('event_manager:edit:form:registration_completed:description'),
	'#class' => 'event-manager-edit-registration-completed event-manager-forms-label-inline hidden',
	'name' => 'registration_completed',
	'value' => $vars['registration_completed'],
]);
