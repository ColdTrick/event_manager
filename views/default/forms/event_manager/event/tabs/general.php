<?php
/**
 * Form fields for general event information
 */

$title_label = elgg_echo('title');
$title_input = elgg_view('input/text', array(
	'name' => 'title',
	'value' => $vars["title"]
));

// Starting time
$start_time_label = elgg_echo('event_manager:edit:form:start');
$start_day_input = elgg_view('input/date', array(
	'name' => 'start_time',
	'value' => $vars["start_time"],
	'class' => 'event_manager_event_edit_date',
	'timestamp' => true,
));
$start_time_input = elgg_view('input/time', array(
	'name' => 'start_time',
	'value' => $vars["start_time"],
));

// Ending time
$end_time_label = elgg_echo('event_manager:edit:form:end');
$end_day_input = elgg_view('input/date', array(
	'name' => 'end_time',
	'value' => $vars["end_time"],
	'class' => 'event_manager_event_edit_date',
	'timestamp' => true,
));
$end_time_input = elgg_view('input/time', array(
	'name' => 'end_time',
	'value' => $vars["end_time"],
));

echo <<<HTML
	<div>
		<label>$title_label *</label>
		$title_input
	</div>
	<div>
		<label>$start_time_label *</label>
		$start_day_input
		$start_time_input
	</div>
	<div>
		<label>$end_time_label *</label>
		$end_day_input
		$end_time_input
	</div>
HTML;
