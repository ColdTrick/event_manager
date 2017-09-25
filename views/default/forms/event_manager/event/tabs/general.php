<?php
/**
 * Form fields for general event information
 */

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('title'),
	'name' => 'title',
	'value' => $vars['title'],
	'required' => true,
]);

$gm_event_start = gmmktime(0,0,0,gmdate('m', $vars['event_start']),gmdate('d', $vars['event_start']),gmdate('Y', $vars['event_start']));
$gm_event_end = gmmktime(0,0,0,gmdate('m', $vars['event_end']),gmdate('d', $vars['event_end']),gmdate('Y', $vars['event_end']));

// Starting time
$event_start_input = elgg_view('input/date', [
	'name' => 'event_start',
	'id' => 'event_start',
	'timestamp' => true,
	'required' => true,
	'value' => $gm_event_start,
	'class' => 'event_manager_event_edit_date',
]);
$start_time_input = elgg_view('input/time', [
	'name' => 'start_time',
	'value' => $vars['event_start'],
]);

$start = elgg_view('elements/forms/field', [
	'label' => elgg_view('elements/forms/label', [
		'label' => elgg_echo('event_manager:edit:form:start'),
		'id' => 'event_start',
		'required' => true,
	]),
	'input' => $event_start_input . $start_time_input,
	'class' => 'event-manager-forms-label-inline man',
]);

// Ending time
$event_end_input = elgg_view('input/date', [
	'name' => 'event_end',
	'id' => 'event_end',
	'timestamp' => true,
	'required' => true,
	'value' => $gm_event_end,
	'class' => 'event_manager_event_edit_date'
]);
$end_time_input = elgg_view('input/time', [
	'name' => 'end_time',
	'value' => $vars['event_end'],
]);

$end = elgg_view('elements/forms/field', [
	'label' => elgg_view('elements/forms/label', [
		'label' => elgg_echo('event_manager:edit:form:end'),
		'id' => 'event_end',
		'required' => true,
	]),
	'input' => $event_end_input . $end_time_input,
	'class' => 'event-manager-forms-label-inline man',
]);

echo "<div class='elgg-col elgg-col-1of2'>{$start}</div>";
echo "<div class='elgg-col elgg-col-1of2'>{$end}</div>";