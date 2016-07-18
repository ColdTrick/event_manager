<?php

gatekeeper();

$title_text = elgg_echo('event_manager:edit:title');

$guid = (int) elgg_extract('guid', $vars);
$event = false;

if (!empty($guid) && ($entity = get_entity($guid))) {
	if (($entity instanceof \Event) && $entity->canEdit()) {
		$event = $entity;

		elgg_push_breadcrumb($entity->title, $event->getURL());

		elgg_set_page_owner_guid($event->container_guid);
	}
}

if (!$event) {
	$page_owner = elgg_get_page_owner_entity();

	if ($page_owner instanceof \ElggGroup) {
		if (!event_manager_can_create_group_events($page_owner)) {
			forward('events');
		}

	} else {
		if (!event_manager_can_create_site_events()) {
			forward('events');
		}
		elgg_set_page_owner_guid(elgg_get_logged_in_user_guid());
	}
}

elgg_push_breadcrumb($title_text);

$form_vars = [
	'id' => 'event_manager_event_edit',
	'name' 	=> 'event_manager_event_edit',
	'enctype' => 'multipart/form-data'
];

$form = elgg_view_form('event_manager/event/edit', $form_vars, ['entity' => $event]);

$sidebar = elgg_view_menu('event_edit', ['sort_by' => 'register']);

$body = elgg_view_layout('content', [
	'filter' => '',
	'content' => $form,
	'title' => $title_text,
	'sidebar' => $sidebar,
]);

echo elgg_view_page($title_text, $body);
