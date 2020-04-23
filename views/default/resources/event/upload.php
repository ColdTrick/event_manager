<?php
elgg_gatekeeper();

$guid = (int) elgg_extract('guid', $vars);

$title_text = elgg_echo('event_manager:edit:upload:title');

elgg_entity_gatekeeper($guid, 'object', Event::SUBTYPE);

$event = get_entity($guid);

if (!$event->canEdit()) {
	throw new \Elgg\EntityPermissionsException();
}

elgg_push_entity_breadcrumbs($event);

$form_vars = [
	'id' => 'event_manager_event_upload',
	'name' => 'event_manager_event_upload',
	'action' => 'action/event_manager/event/upload',
	'enctype' => 'multipart/form-data',
];
$form = elgg_view_form('event_manager/event/upload_file', $form_vars, ['entity' => $event]);

$current_files = elgg_view('event_manager/event/files', ['entity' => $event]);

echo elgg_view_page($title_text, ['content' => $form . $current_files]);
