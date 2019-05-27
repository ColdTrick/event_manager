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

// Starting time
$event_start_id = 'event_start_' . uniqid();
$event_start_input = elgg_view('input/date', [
	'name' => 'event_start',
	'id' => $event_start_id,
	'timestamp' => true,
	'required' => true,
	'value' => $vars['event_start'],
	'class' => 'event_manager_event_edit_date',
]);
$start_time_input = elgg_view('input/time', [
	'name' => 'start_time',
	'value' => $vars['event_start'],
	'timestamp' => true,
]);

$start = elgg_view('elements/forms/field', [
	'label' => elgg_view('elements/forms/label', [
		'label' => elgg_echo('event_manager:edit:form:start'),
		'id' => $event_start_id,
		'required' => true,
	]),
	'input' => $event_start_input . $start_time_input,
	'class' => 'event-manager-forms-label-inline man',
]);

// Ending time
$event_end_id = 'event_end_' . uniqid();
$event_end_input = elgg_view('input/date', [
	'name' => 'event_end',
	'id' => $event_end_id,
	'timestamp' => true,
	'required' => true,
	'value' => $vars['event_end'],
	'class' => 'event_manager_event_edit_date'
]);
$end_time_input = elgg_view('input/time', [
	'name' => 'end_time',
	'value' => $vars['event_end'],
	'timestamp' => true,
]);

$end = elgg_view('elements/forms/field', [
	'label' => elgg_view('elements/forms/label', [
		'label' => elgg_echo('event_manager:edit:form:end'),
		'id' => $event_end_id,
		'required' => true,
	]),
	'input' => $event_end_input . $end_time_input,
	'class' => 'event-manager-forms-label-inline man',
]);

echo "<div class='event-manager-event-edit-global-dates'>";
echo "<div>{$start}</div>";
echo "<div>{$end}</div>";
echo "</div>";