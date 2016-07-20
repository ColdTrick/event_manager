<?php
/**
 * Migrate timestamp of events
 */

$success_count = 0;
$error_count = 0;

if (get_input('upgrade_completed')) {
	// set the upgrade as completed
	$factory = new \ElggUpgrade();
	$upgrade = $factory->getUpgradeFromPath('admin/upgrades/convert_timestamps');
	if ($upgrade instanceof \ElggUpgrade) {
		$upgrade->setCompleted();
	}
	return true;
}

$access_status = access_get_show_hidden_status();
access_show_hidden_entities(true);

$batch = new \ElggBatch('elgg_get_entities', [
	'type' => 'object',
	'subtype' => \Event::SUBTYPE,
	'offset' => (int) get_input('offset', 0),
	'limit' => 25,
]);

foreach ($batch as $event) {
	$success_count++;
	
	if ($event->event_start) {
		// already converted
		continue;
	}
	
	$event_start = $event->start_day;
	if ($event->start_time) {
		$hours = date('H', $event->start_time);
		$minutes = date('i', $event->start_time);
		
		$event_start += $minutes * 60;
		$event_start += $hours * 3600;
	}
	
	$event_end = $event->end_ts;
	if (empty($event_end)) {
		$event_end = $event_start + 3600;
	}
		
	$event->event_start = $event_start;
	$event->event_end = $event_end;
	
	unset($event->end_ts);
	unset($event->start_day);
	unset($event->start_time);
}

access_show_hidden_entities($access_status);

// Give some feedback for the UI
echo json_encode([
	'numSuccess' => $success_count,
	'numErrors' => $error_count,
]);
