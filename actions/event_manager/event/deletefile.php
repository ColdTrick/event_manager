<?php

$guid = (int) get_input('guid');
$filename = get_input('file', null, false);

$event = get_entity($guid);

if (empty($filename) || !($event instanceof \Event) || !$event->canEdit()) {
	return elgg_error_response(elgg_echo('event_manager:event:file:notfound:text'));
}

$files = $event->getFiles();

foreach ($files as $index => $file) {
	if (strtolower($file->file) == strtolower($filename)) {
		$fileHandler = new \ElggFile();
		$fileHandler->owner_guid = $event->guid;
		$fileHandler->setFilename($file->file);
		
		if (!$fileHandler->exists()) {
			// check old storage location
			$fileHandler->setFilename("files/{$file->file}");
		}
		
		if ($fileHandler->delete()) {
			unset($files[$index]);
		}
	}
}

$files = array_values($files);

$event->files = $files ? json_encode($files) : null;

return elgg_ok_response();
