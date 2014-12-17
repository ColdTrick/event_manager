<?php
/**
 * Updates the event starting and ending times
 *
 * - Combines start_time and start_day values
 * - Renames end_ts to end_time
 */

$events = new ElggBatch('elgg_get_entities_from_metadata', array(
	'type' => 'object',
	'subtype' => Event::SUBTYPE,
	'limit' => false,
	'metadata_names' => 'start_day',
));

foreach ($events as $event) {
	// Get the starting time as minutes and hours
	$start_minute = date('i', $event->start_time);
	$start_hour   = date('H', $event->start_time);

	// Get the starting date as days, months and years
	$start_day    = date('j', $event->start_day);
	$start_month  = date('n', $event->start_day);
	$start_year   = date('Y', $event->start_day);

	// Combine the values into a timestamp
	$time = mktime($start_hour, $start_minute, 0, $start_month, $start_day, $start_year);
	$event->start_time = $time;
	$event->start_day = null;

	// Rename the end_ts metadata to end_time for consistency
	$event->end_time = $event->end_ts;
	$event->end_ts = null;

	$event->save();
}
