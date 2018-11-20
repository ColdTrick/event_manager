<?php

$guid = (int) get_input('guid');
$title = get_input('title');

elgg_entity_gatekeeper($guid, 'object', Event::SUBTYPE);

$event = get_entity($guid);

if (!$event->canEdit()) {
	return elgg_error_response(elgg_echo('actionunauthorized'));
}

if (empty($title)) {
	return elgg_error_response(elgg_echo('event_manager:action:event:edit:error_fields'));
}

// check if upload attempted and failed
$uploaded_file = elgg_get_uploaded_file('file', false);
if ($uploaded_file && !$uploaded_file->isValid()) {
	$error = elgg_get_friendly_upload_error($uploaded_file->getError());
	return elgg_error_response($error);
}

if (empty($event->files)) {
	$filesArray = [];
} else {
	$filesArray = json_decode($event->files, true);
}

$file = new \ElggFile();
$file->owner_guid = $event->guid;
$file->acceptUploadedFile($uploaded_file);

$filesArray[] = [
	'title' => $title,
	'file' => $file->getFilename(),
	'mime' => $file->getMimeType(),
	'time_uploaded' => time(),
	'uploader' => elgg_get_logged_in_user_guid(),
];

$event->files = json_encode($filesArray);

return elgg_ok_response('', elgg_echo('event_manager:action:event:edit:ok'));
