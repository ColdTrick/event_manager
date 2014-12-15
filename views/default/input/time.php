<?php
/**
 * Elgg time input
 *
 * Displays two dropdowns, one for selecting the hour and
 * one for selecting the minutes.
 *
 * @uses $vars['name'] Name of the field
 * @uses $vars['value'] Unix timestamp
 */

$name = $vars['name'];
$time = $vars['value'];

$hour = 0;
$minute = 0;

if ($time) {
	$hour = date('H', $time);
	$minute = date('i', $time);
}

// Generate hour options
$hour_options = range(0, 23);
array_walk($hour_options, 'event_manager_time_pad');

// Generate minute options
$minute_options = range(0, 59, 5);
array_walk($minute_options, 'event_manager_time_pad');

echo elgg_view('input/dropdown', array(
	'name' => "{$name}_hours",
	'value' => $hour,
	'options' => $hour_options,
));

echo elgg_view('input/dropdown', array(
	'name' => "{$name}_minutes",
	'value' => $minute,
	'options' => $minute_options,
));
