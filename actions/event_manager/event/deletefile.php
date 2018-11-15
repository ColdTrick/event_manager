<?php

$guid = (int) get_input('guid');
$filename = get_input('file');

$event = get_entity($guid);

if (empty($filename) || !($event instanceof \Event) || !$event->canEdit()) {
	return elgg_error_response(elgg_echo('event_manager:event:file:notfound:text'));
}

$files = json_decode($event->files, true);

foreach ($files as $index => $file) {
	if (strtolower($file["file"]) == strtolower($filename)) {
		$fileHandler = new \ElggFile();
		$fileHandler->owner_guid = $event->guid;
		$fileHandler->setFilename("files/" . $file['file']);

		$fileHandler->delete();
		unset($files[$index]);
	}
}

$event->files = json_encode($files);

return elgg_ok_response();
