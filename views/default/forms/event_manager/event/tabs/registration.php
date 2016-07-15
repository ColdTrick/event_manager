<?php
/**
 * Form fields for entering registration settings
 */

$event = elgg_extract('entity', $vars);

echo '<div class="clearfix"><div class="elgg-col elgg-col-1of4">';
echo elgg_view_input('text', [
	'label' => elgg_echo('event_manager:edit:form:fee'),
	'name' => 'fee',
	'value' => $vars['fee'],
]);
echo '</div>';
echo '<div class="elgg-col elgg-col-3of4">';
$field_class = 'pll';
if (empty($vars['fee'])) {
	$field_class .= ' hidden';
}
echo elgg_view_input('text', [
	'label' => elgg_echo('event_manager:edit:form:fee_details'),
	'name' => 'fee_details',
	'value' => $vars['fee_details'],
	'field_class' => $field_class,
]);
echo '</div></div>';

echo '<div class="clearfix"><div class="elgg-col elgg-col-1of4">';
echo elgg_view_input('text', [
	'label' => elgg_echo('event_manager:edit:form:max_attendees'),
	'name' => 'max_attendees',
	'value' => $vars['max_attendees'],
]);
echo '</div>';
echo '<div class="elgg-col elgg-col-3of4">';
$field_class = 'pll';
if (empty($vars['max_attendees']) && empty($vars['waiting_list_enabled'])) {
	$field_class .= ' hidden';
}
echo elgg_view_input('checkboxes', [
	'name' => 'waiting_list_enabled',
	'value' => $vars['waiting_list_enabled'],
	'options' => [elgg_echo('event_manager:edit:form:waiting_list') => '1'],
	'field_class' => $field_class,
	'class' => 'mts',
	'label' => '&nbsp;',
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

echo elgg_view('elements/forms/field', [
	'label' => elgg_view('elements/forms/label', [
		'label' => elgg_echo('event_manager:edit:form:registration_options'),
	]),
	'input' => $with_program . $registration_needed  . $register_nologin,
	'class' => 'event-manager-forms-label-normal',
]);

echo elgg_view_input('date', [
	'label' => elgg_echo('event_manager:edit:form:endregistration_day'),
	'name' => 'endregistration_day',
	'id' => 'endregistration_day',
	'value' => $vars['endregistration_day'],
]);

$registration_ended = elgg_view('input/checkboxes', [
	'name' => 'registration_ended',
	'value' => $vars['registration_ended'],
	'options' => [elgg_echo('event_manager:edit:form:registration_ended') => '1'],
]);

$event_interested = elgg_view('input/checkboxes', [
	'name' => 'event_interested',
	'id' => 'event_interested',
	'value' => $vars['event_interested'],
	'options' => [elgg_echo('event_manager:event:relationship:event_interested') => '1'],
]);

$event_presenting = elgg_view('input/checkboxes', [
	'name' => 'event_presenting',
	'id' => 'event_presenting',
	'value' => $vars['event_presenting'],
	'options' => [elgg_echo('event_manager:event:relationship:event_presenting') => '1'],
]);

$event_exhibiting = elgg_view('input/checkboxes', [
	'name' => 'event_exhibiting',
	'id' => 'event_exhibiting',
	'value' => $vars['event_exhibiting'],
	'options' => [elgg_echo('event_manager:event:relationship:event_exhibiting') => '1'],
]);

$event_organizing = elgg_view('input/checkboxes', [
	'name' => 'event_organizing',
	'id' => 'event_organizing',
	'value' => $vars['event_organizing'],
	'options' => [elgg_echo('event_manager:event:relationship:event_organizing') => '1'],
]);

echo elgg_view('elements/forms/field', [
	'label' => elgg_view('elements/forms/label', [
		'label' => elgg_echo('event_manager:edit:form:rsvp_options'),
	]),
	'input' => $registration_ended . $event_interested . $event_presenting . $event_exhibiting . $event_organizing,
	'class' => 'event-manager-forms-label-normal',
]);
