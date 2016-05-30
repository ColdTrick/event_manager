<?php

$guid = (int) get_input('guid');
$title = get_input('title');

elgg_entity_gatekeeper($guid, 'object', Event::SUBTYPE);

$event = get_entity($guid);

if (!$event->canEdit()) {
	register_error(elgg_echo('actionunauthorized'));
	forward(REFERER);
}

if (empty($title) || (!isset($_FILES['file']['name']) || empty($_FILES['file']['name']))) {
	register_error(elgg_echo('event_manager:action:event:edit:error_fields'));
	forward(REFERER);
}

if (empty($event->files)) {
	$filesArray = [];
} else {
	$filesArray = json_decode($event->files, true);
}

$newFilename = event_manager_sanitize_filename($_FILES['file']['name']);

$fileHandler = new \ElggFile();
$fileHandler->setFilename('files/' . $newFilename);
$fileHandler->owner_guid = $event->guid;
$fileHandler->open('write');
$fileHandler->write(get_uploaded_file('file'));
$fileHandler->close();

$filesArray[] = [
	'title' => $title,
	'file' => $newFilename,
	'mime' => $_FILES['file']['type'],
	'time_uploaded' => time(),
	'uploader' => elgg_get_logged_in_user_guid(),
];

$event->files = json_encode($filesArray);

system_message(elgg_echo('event_manager:action:event:edit:ok'));
forward(REFERER);
