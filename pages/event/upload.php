<?php
gatekeeper();

$guid = (int) get_input('guid');

$title_text = elgg_echo('event_manager:edit:upload:title');

$event = get_entity($guid);
if (empty($event) || ($event->getSubtype() !== Event::SUBTYPE)) {
	register_error(elgg_echo('InvalidParameterException:GUIDNotFound', array($guid)));
	forward(REFERER);
}

if (!$event->canEdit()) {
	forward($event->getURL());
}

elgg_push_breadcrumb($event->title, $event->getURL());
elgg_push_breadcrumb($title_text);

$form_vars = [
	'id' => 'event_manager_event_upload',
	'name' => 'event_manager_event_upload',
	'action' => 'action/event_manager/event/upload',
	'enctype' => 'multipart/form-data',
];
$form = elgg_view_form('event_manager/event/upload_file', $form_vars, ['entity' => $event]);

$current_files = elgg_view('event_manager/event/files', ['entity' => $event]);

$body = elgg_view_layout('content', [
	'filter' => '',
	'content' => $form . $current_files,
	'title' => $title_text,
]);

echo elgg_view_page($title_text, $body);
