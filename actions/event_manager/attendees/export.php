<?php
$guid = (int) get_input('guid');
$rel = get_input('rel', EVENT_MANAGER_RELATION_ATTENDING);

elgg_entity_gatekeeper($guid, 'object', Event::SUBTYPE);
$event = get_entity($guid);

if (!$event->canEdit()) {
	return elgg_error_response(elgg_echo('actionunauthorized'));
}

$rows = elgg_call(ELGG_IGNORE_ACCESS, function() use ($rel, $event) {
	$rows = [];
	
	$attendees = new \ElggBatch('elgg_get_entities', [
		'relationship' => $rel,
		'relationship_guid' => $event->guid,
		'inverse_relationship' => false,
		'limit' => false,
	]);
	
	foreach ($attendees as $attendee) {
		$params = [
			'event' => $event,
			'attendee' => $attendee,
			'relationship' => $rel,
		];
		
		$rowdata = elgg_trigger_plugin_hook('export_attendee', 'event', $params, []);;
		if (empty($rowdata)) {
			continue;
		}
		
		$rows[] = $rowdata;
	}
	
	return $rows;
});

if (empty($rows)) {
	return elgg_error_response(elgg_echo('event_manager:action:attendees:export:no_data'));
}

$fh = tmpfile();

fputcsv($fh, array_keys($rows[0]), ';');

foreach ($rows as $row) {
	fputcsv($fh, array_values($row), ';');
}

$contents = '';
rewind($fh);
while (!feof($fh)) {
	$contents .= fread($fh, 2048);
}

fclose($fh);

// create export file
header('Content-Type: text/csv');
header('Content-Disposition: Attachment; filename="attendees-' . elgg_get_friendly_title($event->getDisplayName()) . '.csv"');
header('Content-Length: ' . strlen($contents));
header('Pragma: public');

echo $contents;

exit();
