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

$name = elgg_extract('name', $vars);
$time = elgg_extract('value', $vars);

$hour = 0;
$minute = 0;

if ($time) {
	$hour = gmdate('H', $time);
	$minute = gmdate('i', $time);
}

// Generate hour options
$hour_options = range(0, 23);
array_walk($hour_options, function(&$value) {
	$value = str_pad($value, 2, '0', STR_PAD_LEFT);
});

// Generate minute options
$minute_options = range(0, 59, 5);
array_walk($minute_options, function(&$value) {
	$value = str_pad($value, 2, '0', STR_PAD_LEFT);
});

echo elgg_view('input/select', [
	'name' => "{$name}_hours",
	'value' => $hour,
	'options' => $hour_options,
]);

echo elgg_view('input/select', [
	'name' => "{$name}_minutes",
	'value' => $minute,
	'options' => $minute_options,
]);
