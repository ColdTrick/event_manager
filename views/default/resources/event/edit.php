<?php

$guid = (int) elgg_extract('guid', $vars);

/** @var \Event $event */
$event = elgg_entity_gatekeeper($guid, 'object', \Event::SUBTYPE, true);

elgg_set_page_owner_guid($event->container_guid);

elgg_push_entity_breadcrumbs($event);

elgg_register_menu_item('title', [
	'name' => 'copy',
	'icon' => 'clone',
	'text' => elgg_echo('event_manager:menu:copy'),
	'href' => elgg_generate_url('ajax', [
		'type' => 'form',
		'segments' => 'event_manager/event/copy',
		'guid' => $event->guid,
	]),
	'link_class' => 'elgg-lightbox elgg-button elgg-button-action',
]);

echo elgg_view_page(elgg_echo('event_manager:edit:title'), [
	'content' => elgg_view_form('event_manager/event/edit', [
		'id' => 'event_manager_event_edit',
		'name' 	=> 'event_manager_event_edit',
	], [
		'entity' => $event,
	]),
	'filter_id' => 'event/edit',
]);
