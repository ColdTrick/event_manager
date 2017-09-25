<?php
/**
 * Migrate event files from owner folder to event folder in dataroot
 *
 */

$success_count = 0;

$access_status = access_get_show_hidden_status();
access_show_hidden_entities(true);

$session = elgg_get_session();
$offset = (int) $session->get('event_manager_files_migration_offset', 0);
$start_offset = $offset;

$batch = new \ElggBatch('elgg_get_entities_from_metadata', [
	'type' => 'object',
	'subtype' => \Event::SUBTYPE,
	'metadata_names' => ['icontime', 'files'],
	'limit' => 25,
	'offset' => $offset,
]);


$old_file = new \ElggFile();
$new_file = new \ElggFile();

foreach ($batch as $event) {
	
	$old_file->owner_guid = $event->getOwnerGUID();
	$new_file->owner_guid = $event->getGUID();
	
	if ($event->icontime) {
		$icon_sizes = elgg_get_icon_sizes('object', 'event');
		
		foreach ($icon_sizes as $icon_name => $icon_info) {
			
			$old_file->setFilename("events/{$event->guid}/{$icon_name}.jpg");
		
			if ($old_file->exists()) {
				$new_file->setFilename("{$icon_name}.jpg");
				$new_file->open('write');
				if ($new_file->write($old_file->grabFile())) {
					$old_file->delete();
				}
				$new_file->close();
			}
		}
	}
	
	if ($event->files) {
		$files = json_decode($event->files, true);

		if (!empty($files)) {
			foreach ($files as $file) {
				$file_name = $file['file'];
				$old_file->setFilename("events/{$event->guid}/files/{$file_name}");
				
				if ($old_file->exists()) {
					$new_file->setFilename("files/{$file_name}");
					$new_file->open('write');
					if ($new_file->write($old_file->grabFile())) {
						$old_file->delete();
					}
					$new_file->close();
				}
			}
		}
	}
	
	$offset++;
	$session->set('event_manager_files_migration_offset', $offset);
	
	$success_count++;
}

access_show_hidden_entities($access_status);

if ($start_offset === $offset) {
	// no new entities found to process
	// set the upgrade as completed
	$factory = new \ElggUpgrade();
	$upgrade = $factory->getUpgradeFromPath('admin/upgrades/migrate_files_to_event');
	if ($upgrade instanceof \ElggUpgrade) {
		$upgrade->setCompleted();
	}

	return true;
}

// Give some feedback for the UI
echo json_encode([
	'numSuccess' => $success_count,
	'numErrors' => 0,
]);
