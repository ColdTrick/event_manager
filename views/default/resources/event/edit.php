<?php

$guid = (int) elgg_extract('guid', $vars);
elgg_entity_gatekeeper($guid, 'object', \Event::SUBTYPE, true);

/* @var $event \Event */
$event = get_entity($guid);

elgg_set_page_owner_guid($event->container_guid);

elgg_push_entity_breadcrumbs($event);

// add copy menu item
elgg_register_menu_item('title', [
	'name' => 'copy',
	'icon' => 'clone-regular',
	'href' => 'ajax/form/event_manager/event/copy?guid=' . $event->guid,
	'text' => elgg_echo('event_manager:menu:copy'),
	'link_class' => 'elgg-lightbox elgg-button elgg-button-action',
]);

$form_vars = [
	'id' => 'event_manager_event_edit',
	'name' 	=> 'event_manager_event_edit',
];

$form = elgg_view_form('event_manager/event/edit', $form_vars, ['entity' => $event]);

echo elgg_view_page(elgg_echo('event_manager:edit:title'), [
	'content' => $form,
	'filter_id' => 'event/edit',
]);
