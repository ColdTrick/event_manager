<?php

$guid = (int) elgg_extract('guid', $vars);

elgg_entity_gatekeeper($guid, 'object', Event::SUBTYPE);
$entity = get_entity($guid);

if (!$entity->register_nologin) {
	forward(REFERER);
}

// set page owner
elgg_set_page_owner_guid($entity->getContainerGUID());

// make breadcrumb
elgg_push_breadcrumb($entity->title, $entity->getURL());

// build page elements
$title_text = elgg_echo('event_manager:unsubscribe:title', [$entity->title]);

$body = elgg_view_form('event_manager/event/unsubscribe', [], ['entity' => $entity]);

$page_data = elgg_view_layout('content', [
	'title' => $title_text,
	'content' => $body,
	'filter' => '',
]);

echo elgg_view_page($title_text, $page_data, 'default');
