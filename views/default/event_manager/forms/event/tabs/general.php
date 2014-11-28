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
	'name' => 'start_day',
	'id' => 'start_day',
	'value' => $vars["start_day"],
	"class" => "event_manager_event_edit_date"
));
$start_time_hour_input = event_manager_get_form_pulldown_hours('start_time_hours', date('H', $vars["start_time"]));
$start_time_minute_input = event_manager_get_form_pulldown_minutes('start_time_minutes', date('i', $vars["start_time"]));

// Ending time
$end_time_label = elgg_echo('event_manager:edit:form:end');
$end_day_input = elgg_view('input/date', array(
	'name' => 'end_day',
	'id' => 'end_day',
	'value' => $vars["end_day"],
	"class" => "event_manager_event_edit_date"
));
$end_time_hour_input = event_manager_get_form_pulldown_hours('end_time_hours', date('H', $vars["end_time"]));
$end_time_minute_input = event_manager_get_form_pulldown_minutes('end_time_minutes', date('i', $vars["end_time"]));

echo <<<HTML
	<div>
		<label>$title_label *</label>
		$title_input
	</div>
	<div>
		<label>$start_time_label *</label>
		$start_day_input
		$start_time_hour_input
		$start_time_minute_input
	</div>
	<div>
		<label>$end_time_label *</label>
		$end_day_input
		$end_time_hour_input
		$end_time_minute_input
	</div>
HTML;
