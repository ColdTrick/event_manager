<?php

elgg_require_js('event_manager/list_events');
elgg_load_js('event_manager.maps.base');
elgg_require_js('event_manager/googlemaps');

$title_text = elgg_echo('event_manager:list:title');

$event_options = [];

if (($page_owner = elgg_get_page_owner_entity()) && ($page_owner instanceof ElggGroup)) {
	group_gatekeeper();
	$title_text = elgg_echo('event_manager:list:group:title');

	elgg_push_breadcrumb($page_owner->name, $page_owner->getURL());

	$event_options['container_guid'] = $page_owner->getGUID();

	$who_create_group_events = elgg_get_plugin_setting('who_create_group_events', 'event_manager'); // group_admin, members
	if ((($who_create_group_events == 'group_admin') && $page_owner->canEdit()) || (($who_create_group_events == 'members') && $page_owner->isMember($user))) {
		elgg_register_menu_item('title', [
			'name' => 'new',
			'href' => 'events/event/new/' . $page_owner->getGUID(),
			'text' => elgg_echo('event_manager:menu:new_event'),
			'link_class' => 'elgg-button elgg-button-action',
		]);
	}
} elseif (elgg_is_logged_in()) {
	$who_create_site_events = elgg_get_plugin_setting('who_create_site_events', 'event_manager');
	if ($who_create_site_events != 'admin_only' || elgg_is_admin_logged_in()) {
		elgg_register_menu_item('title', [
			'name' => 'new',
			'href' => 'events/event/new',
			'text' => elgg_echo('event_manager:menu:new_event'),
			'link_class' => 'elgg-button elgg-button-action',
		]);
	}
}

$events = event_manager_search_events($event_options);

$content = elgg_view('event_manager/forms/event/search');

$content .= elgg_view('event_manager/list', [
	'entities' => $events['entities'], 
	'count' => $events['count']
]);

$body = elgg_view_layout('content', [
	'filter' => '',
	'content' => $content,
	'title' => $title_text,
]);

echo elgg_view_page($title_text, $body);
