<?php

gatekeeper();

$title_text = elgg_echo("event_manager:edit:title");

$guid = (int) elgg_extract('guid', $vars);
$event = false;

if (!empty($guid) && ($entity = get_entity($guid))) {
	if (($entity->getSubtype() == Event::SUBTYPE) && $entity->canEdit()) {
		$event = $entity;

		elgg_push_breadcrumb($entity->title, $event->getURL());

		elgg_set_page_owner_guid($event->container_guid);
	}
}

if (!$event) {
	$forward = true;
	$page_owner = elgg_get_page_owner_entity();

	if ($page_owner instanceof \ElggGroup) {
		$who_create_group_events = elgg_get_plugin_setting('who_create_group_events', 'event_manager'); // group_admin, members

		if (!empty($who_create_group_events)) {
			if ((($who_create_group_events == 'group_admin') && $page_owner->canEdit()) || ((($who_create_group_events == 'members') && $page_owner->isMember()) || $page_owner->canEdit())) {
				$forward = false;
			}
		}

	} else {
		$who_create_site_events = elgg_get_plugin_setting('who_create_site_events', 'event_manager');
		if (($who_create_site_events != 'admin_only') || elgg_is_admin_logged_in()) {
			$forward = false;
		}
		elgg_set_page_owner_guid(elgg_get_logged_in_user_guid());
	}

	if ($forward) {
		forward('events');
	}
}

elgg_push_breadcrumb($title_text);

$form_vars = [
	'id' => 'event_manager_event_edit',
	'name' 	=> 'event_manager_event_edit',
	'enctype' => 'multipart/form-data'
];

$form = elgg_view_form('event_manager/event/edit', $form_vars, ['entity' => $event]);

$sidebar = elgg_view_menu('event_edit', [
	'id' => 'event-manager-forms-event-edit',
	'sort_by' => 'register',
]);

$body = elgg_view_layout('content', [
	'filter' => '',
	'content' => $form,
	'title' => $title_text,
	'sidebar' => $sidebar,
]);

echo elgg_view_page($title_text, $body);
