<?php

$guid = (int) get_input('guid');
$title = get_input('title');
$description = get_input('description');
$recipients = (array) get_input('recipients');

$entity = get_entity($guid);
if (!$entity instanceof \Event || !$entity->canEdit()) {
	return elgg_error_response(elgg_echo('actionunauthorized'));
}

if (empty($title) || empty($description) || empty($recipients)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

$mail = new \EventMail();
$mail->container_guid = $entity->guid;

$mail->title = $title;
$mail->description = $description;
$mail->recipients = $recipients;

if (!$mail->save()) {
	return elgg_error_response(elgg_echo('save:fail'));
}

return elgg_ok_response('', elgg_echo('event_manager:action:event:mail:success'), $entity->getURL());
