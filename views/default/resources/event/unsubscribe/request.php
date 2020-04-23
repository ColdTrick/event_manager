<?php

$guid = (int) elgg_extract('guid', $vars);

elgg_entity_gatekeeper($guid, 'object', Event::SUBTYPE);
$entity = get_entity($guid);

if (!$entity->register_nologin) {
	forward(REFERER);
}

// set page owner
elgg_set_page_owner_guid($entity->getContainerGUID());

elgg_push_entity_breadcrumbs($entity);

// build page elements
$title_text = elgg_echo('event_manager:unsubscribe:title', [$entity->getDisplayName()]);

$body = elgg_view_form('event_manager/event/unsubscribe', [], ['entity' => $entity]);

echo elgg_view_page($title_text, ['content' => $body]);
