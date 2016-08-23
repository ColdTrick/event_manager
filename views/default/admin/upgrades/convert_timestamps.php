<?php

// Upgrade also possible hidden entities. This feature get run
// by an administrator so there's no need to ignore access.
$access_status = access_get_show_hidden_status();
access_show_hidden_entities(true);

$count = elgg_get_entities_from_metadata([
	'type' => 'object',
	'subtype' => \Event::SUBTYPE,
	'count' => true,
	'metadata_names' => ['start_day'],
]);

echo elgg_view('output/longtext', ['value' => elgg_echo('admin:upgrades:convert_timestamps:description')]);

echo elgg_view('admin/upgrades/view', [
	'count' => $count,
	'action' => 'action/event_manager/upgrades/convert_timestamps',
]);
access_show_hidden_entities($access_status);