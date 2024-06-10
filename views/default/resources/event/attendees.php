<?php

use Elgg\Exceptions\Http\BadRequestException;

$guid = elgg_extract('guid', $vars);
elgg_entity_gatekeeper($guid, 'object', \Event::SUBTYPE);

/* @var $entity \Event */
$entity = get_entity($guid);

$relationship = elgg_extract('relationship', $vars);
$valid_relationships = $entity->getSupportedRelationships();
if (!array_key_exists($relationship, $valid_relationships)) {
	$exception = new BadRequestException();
	$exception->setRedirectUrl(elgg_generate_url('collection:object:event:attendees', [
		'guid' => $entity->guid,
		'relationship' => EVENT_MANAGER_RELATION_ATTENDING,
	]));
	throw $exception;
}

$rel_text = $valid_relationships[$relationship];

elgg_set_page_owner_guid($entity->container_guid);

elgg_push_entity_breadcrumbs($entity);

if ($entity->canEdit()) {
	elgg_register_menu_item('title', [
		'name' => 'export',
		'title' => elgg_echo('event_manager:event:exportattendees'),
		'icon' => 'download',
		'text' => elgg_echo('export'),
		'href' => elgg_generate_action_url('event_manager/attendees/export', [
			'guid' => $entity->guid,
			'rel' => $relationship,
		]),
		'link_class' => [
			'elgg-button',
			'elgg-button-action',
		],
	]);
}

// search form
$content = elgg_view_form('event_manager/event/attendees', [
	'action' => elgg_generate_url('collection:object:event:attendees', [
		'guid' => $entity->guid,
		'relationship' => $relationship,
	]),
	'method' => 'GET',
	'disable_security' => true,
	'prevent_double_submit' => false,
], [
	'entity' => $entity,
	'relationship' => $relationship,
]);

// attendees listing
$content .= elgg_view('event_manager/event/attendees_list', [
	'entity' => $entity,
	'relationship' => $relationship,
]);

echo elgg_view_page(elgg_echo('event_manager:event:attendees:title', [$entity->getDisplayName(), $rel_text]), [
	'content' => $content,
	'filter' => elgg_view_menu('event_attendees', [
		'sort_by' => 'priority',
		'class' => 'elgg-menu-hz elgg-tabs',
		'entity' => $entity,
		'relationship' => $relationship,
	]),
]);
