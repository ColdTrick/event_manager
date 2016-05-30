<?php

elgg_get_session()->set('event_manager_files_migration_offset', 0);

// Upgrade also possible hidden entities. This feature get run
// by an administrator so there's no need to ignore access.
$access_status = access_get_show_hidden_status();
access_show_hidden_entities(true);

$count = elgg_get_entities_from_metadata([
	'type' => 'object',
	'subtype' => \Event::SUBTYPE,
	'metadata_names' => ['icontime', 'files'],
	'count' => true,
]);

echo elgg_view('output/longtext', ['value' => elgg_echo('admin:upgrades:migrate_files_to_event:description')]);

echo elgg_view('admin/upgrades/view', [
	'count' => $count,
	'action' => 'action/event_manager/upgrades/files_migration',
]);
access_show_hidden_entities($access_status);