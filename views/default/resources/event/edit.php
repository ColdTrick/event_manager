<?php

elgg_gatekeeper();

$title_text = elgg_echo('event_manager:edit:title');

$guid = (int) elgg_extract('guid', $vars);

// existing event
elgg_entity_gatekeeper($guid, 'object', \Event::SUBTYPE);
$event = get_entity($guid);
if (!$event->canEdit()) {
	throw new \Elgg\EntityPermissionsException();
}

elgg_set_page_owner_guid($event->container_guid);

elgg_push_entity_breadcrumbs($event);

// add copy menu item
elgg_register_menu_item('title', \ElggMenuItem::factory([
	'name' => 'copy',
	'icon' => 'clone-regular',
	'href' => 'ajax/form/event_manager/event/copy?guid=' . $event->guid,
	'text' => elgg_echo('event_manager:menu:copy'),
	'link_class' => 'elgg-lightbox elgg-button elgg-button-action',
]));

$form_vars = [
	'id' => 'event_manager_event_edit',
	'name' 	=> 'event_manager_event_edit',
	'enctype' => 'multipart/form-data'
];

$form = elgg_view_form('event_manager/event/edit', $form_vars, ['entity' => $event]);

echo elgg_view_page($title_text, ['content' => $form]);
