<?php

elgg_gatekeeper();

$title_text = elgg_echo('event_manager:edit:title');

// new event
$page_owner = elgg_get_page_owner_entity();

if (!$page_owner->canWriteToContainer(0, 'object', 'subtype')) {
	throw new \Elgg\EntityPermissionsException();
}

elgg_push_collection_breadcrumbs('object', 'event', $page_owner, false);

$form = elgg_view_form('event_manager/event/edit', [
	'id' => 'event_manager_event_edit',
	'name' 	=> 'event_manager_event_edit',
	'enctype' => 'multipart/form-data'
]);

echo elgg_view_page($title_text, ['content' => $form]);
