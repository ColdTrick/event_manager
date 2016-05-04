<?php
/**
 * Form fields for general event information
 */

echo elgg_view_input('text', [
	'label' => elgg_echo('title'),
	'name' => 'title',
	'value' => $vars['title'],
	'required' => true,
]);

// Starting time
$start_day_input = elgg_view('input/date', [
	'name' => 'start_day',
	'id' => 'start_day',
	'value' => $vars['start_day'],
	'class' => 'event_manager_event_edit_date',
]);
$start_time_input = elgg_view('input/time', [
	'name' => 'start_time',
	'value' => $vars['start_time'],
]);

echo elgg_view('elements/forms/field', [
	'label' => elgg_view('elements/forms/label', [
		'label' => elgg_echo('event_manager:edit:form:start'),
		'id' => 'start_day',
		'required' => true,
	]),
	'input' => $start_day_input . $start_time_input,
	'class' => 'event-manager-forms-label-inline',
]);

// Ending time
$end_day_input = elgg_view('input/date', [
	'name' => 'end_day',
	'id' => 'end_day',
	'value' => $vars['end_day'],
	'class' => 'event_manager_event_edit_date'
]);
$end_time_input = elgg_view('input/time', [
	'name' => 'end_time',
	'value' => $vars['end_ts'],
]);

echo elgg_view('elements/forms/field', [
	'label' => elgg_view('elements/forms/label', [
		'label' => elgg_echo('event_manager:edit:form:end'),
		'id' => 'end_day',
		'required' => true,
	]),
	'input' => $end_day_input . $end_time_input,
	'class' => 'event-manager-forms-label-inline',
]);
