<?php

$guid = (int) get_input('guid');
$filename = get_input('file');

$event = get_entity($guid);

if (empty($filename) || !($event instanceof \Event) || !$event->canEdit()) {
	return elgg_error_response(elgg_echo('event_manager:event:file:notfound:text'));
}

$files = $event->getFiles();

foreach ($files as $index => $file) {
	if (strtolower($file->file) == strtolower($filename)) {
		$fileHandler = new \ElggFile();
		$fileHandler->owner_guid = $event->guid;
		$fileHandler->setFilename("files/{$file->file}");

		$fileHandler->delete();
		unset($files[$index]);
	}
}

$files = array_values($files);

$event->files = $files ? json_encode($files) : null;

return elgg_ok_response();
