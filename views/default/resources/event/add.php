<?php

elgg_gatekeeper();

$title_text = elgg_echo('event_manager:edit:title');

// new event
$page_owner = elgg_get_page_owner_entity();

if (!$page_owner->canWriteToContainer(0, 'object', 'subtype')) {
	throw new \Elgg\EntityPermissionsException();
}

$form_vars = [
	'id' => 'event_manager_event_edit',
	'name' 	=> 'event_manager_event_edit',
	'enctype' => 'multipart/form-data'
];

$form = elgg_view_form('event_manager/event/edit', $form_vars);

$body = elgg_view_layout('default', [
	'filter' => false,
	'content' => $form,
	'title' => $title_text,
]);

echo elgg_view_page($title_text, $body);
