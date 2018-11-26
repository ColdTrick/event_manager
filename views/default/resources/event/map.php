<?php

$title_text = elgg_echo('event_manager:list:title');

$event_options = [];

$page_owner = elgg_get_page_owner_entity() ?: null;
if ($page_owner instanceof \ElggGroup) {
	elgg_group_gatekeeper();
	$title_text = elgg_echo('event_manager:list:group:title');

	$event_options['container_guid'] = $page_owner->guid;
}

elgg_push_collection_breadcrumbs('object', 'event', $page_owner, false);

elgg_register_title_button('event', 'add', 'object', 'event');

$content = elgg_view('event_manager/onthemap', $vars);

$body = elgg_view_layout('default', [
	'filter_id' => 'events',
	'content' => $content,
	'title' => $title_text,
]);

echo elgg_view_page($title_text, $body);
