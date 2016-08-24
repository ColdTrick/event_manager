<?php

gatekeeper();

$title_text = elgg_echo('event_manager:edit:title');

$guid = (int) elgg_extract('guid', $vars);
$event = null;

if (!empty($guid)) {
	// existing event
	elgg_entity_gatekeeper($guid, 'object', \Event::SUBTYPE);
	$event = get_entity($guid);
	if (!$event->canEdit()) {
		register_error(elgg_echo('actionunauthorized'));
		forward(REFERER);
	}
	
	elgg_push_breadcrumb($event->title, $event->getURL());
	elgg_set_page_owner_guid($event->container_guid);
	
	// add copy menu item
	elgg_register_menu_item('title', \ElggMenuItem::factory([
		'name' => 'copy',
		'href' => 'action/event_manager/event/copy?guid=' . $event->getGUID(),
		'confirm' => true,
		'text' => elgg_echo('event_manager:menu:copy'),
		'link_class' => 'elgg-button elgg-button-action',
	]));
} else {
	// new event
	$page_owner = elgg_get_page_owner_entity();

	if ($page_owner instanceof \ElggGroup) {
		if (!event_manager_can_create_group_events($page_owner)) {
			register_error(elgg_echo('actionunauthorized'));
			forward('events');
		}

	} else {
		if (!event_manager_can_create_site_events()) {
			register_error(elgg_echo('actionunauthorized'));
			forward('events');
		}
		elgg_set_page_owner_guid(elgg_get_logged_in_user_guid());
	}
}

$form_vars = [
	'id' => 'event_manager_event_edit',
	'name' 	=> 'event_manager_event_edit',
	'enctype' => 'multipart/form-data'
];

$form = elgg_view_form('event_manager/event/edit', $form_vars, ['entity' => $event]);

$sidebar = elgg_view_menu('event_edit', ['sort_by' => 'register', 'entity' => $event]);

$body = elgg_view_layout('content', [
	'filter' => '',
	'content' => $form,
	'title' => $title_text,
	'sidebar' => $sidebar,
]);

echo elgg_view_page($title_text, $body);
