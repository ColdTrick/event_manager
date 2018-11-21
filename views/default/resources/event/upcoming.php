<?php

elgg_require_js('event_manager/list_events');

$title_text = elgg_echo('event_manager:list:title');

$event_options = [];

$page_owner = elgg_get_page_owner_entity();
if ($page_owner instanceof \ElggGroup) {
	elgg_group_gatekeeper();
	$title_text = elgg_echo('event_manager:list:group:title');

	elgg_push_breadcrumb($page_owner->getDisplayName(), $page_owner->getURL());
	$event_options['container_guid'] = $page_owner->guid;
}

elgg_register_title_button('event', 'add', 'object', 'event');

$events = event_manager_search_events($event_options);
$content = elgg_view('event_manager/list', [
	'entities' => $events['entities'],
	'count' => $events['count'],
]);

$menu = elgg_view_menu('events_list', ['class' => 'elgg-tabs', 'sort_by' => 'register']);

$body = elgg_view_layout('content', [
	'filter' => $menu,
	'content' => $content,
	'title' => $title_text,
]);

echo elgg_view_page($title_text, $body);