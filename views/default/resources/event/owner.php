<?php

$user = elgg_get_page_owner_entity();
if (!$user instanceof \ElggUser) {
	forward('', '404');
}

elgg_register_title_button('event', 'add', 'object', \Event::SUBTYPE);

elgg_push_collection_breadcrumbs('object', 'event', $user);

$filter_value = '';

$title = elgg_echo('event_manager:owner:title', [$user->getDisplayName()]);
if ($user->guid === elgg_get_logged_in_user_guid()) {
	$filter_value = 'mine';
}

$content = elgg_list_entities([
	'type' => 'object',
	'subtype' => \Event::SUBTYPE,
	'owner_guid' => $user->guid,
	'no_results' => true,
]);

$body = elgg_view_layout('default', [
	'content' => $content,
	'title' => $title,
	'filter_value' => $filter_value,
	'filter_id' => 'events',
]);

echo elgg_view_page($title, $body);
