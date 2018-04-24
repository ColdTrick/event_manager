<?php

$guid = elgg_extract('guid', $vars);
elgg_entity_gatekeeper($guid, 'object', Event::SUBTYPE);

/* @var $entity Event */
$entity = get_entity($guid);

$relationship = elgg_extract('relationship', $vars);
$valid_relationships = $entity->getSupportedRelationships();
if (!array_key_exists($relationship, $valid_relationships)) {
	forward("events/event/attendees/{$entity->guid}/" . EVENT_MANAGER_RELATION_ATTENDING);
}
$rel_text = $valid_relationships[$relationship];

// page owner
elgg_set_page_owner_guid($entity->container_guid);

$page_owner = elgg_get_page_owner_entity();
if ($page_owner instanceof ElggGroup) {
	elgg_group_gatekeeper();
	
	elgg_push_breadcrumb($page_owner->getDisplayName(), "/events/event/list/{$page_owner->guid}");
}

// breadcrumb
elgg_push_breadcrumb($entity->getDisplayName(), $entity->getURL());

// title menu item
if ($entity->canEdit()) {
	elgg_register_menu_item('title', [
		'name' => 'export',
		'title' => elgg_echo('event_manager:event:exportattendees'),
		'text' => elgg_view_icon('download', ['class' => 'mrs']) . elgg_echo('export'),
		'href' => elgg_http_add_url_query_elements('action/event_manager/attendees/export', [
			'guid' => $entity->guid,
			'rel' => $relationship,
		]),
		'link_class' => [
			'elgg-button',
			'elgg-button-action',
		],
		'is_action' => true,
	]);
}

// build page elements
$title = elgg_echo('event_manager:event:attendees:title', [$entity->getDisplayName(), $rel_text]);

// search form
$content = elgg_view_form('event_manager/event/attendees', [
	'action' => "events/event/attendees/{$entity->guid}/{$relationship}",
	'method' => 'GET',
	'disable_security' => true,
], [
	'entity' => $entity,
	'relationship' => $relationship,
]);

// attendees listing
$content .= elgg_view('event_manager/event/attendees_list', [
	'entity' => $entity,
	'relationship' => $relationship,
]);

$tabs = elgg_view_menu('event_attendees', [
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz elgg-tabs',
	'entity' => $entity,
	'relationship' => $relationship,
]);

// build page
$page_data = elgg_view_layout('content', [
	'title' => $title,
	'content' => $content,
	'filter' => $tabs,
]);

// draw page
echo elgg_view_page($title, $page_data);
