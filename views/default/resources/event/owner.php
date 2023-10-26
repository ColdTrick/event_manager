<?php

$user = elgg_get_page_owner_entity();

elgg_register_title_button('add', 'object', \Event::SUBTYPE);

elgg_push_collection_breadcrumbs('object', 'event', $user);

$filter_value = '';
if ($user->guid === elgg_get_logged_in_user_guid()) {
	$filter_value = 'mine';
}

$list_type = get_input('list_type', 'list');

$content = elgg_view("event_manager/listing/{$list_type}", [
	'options' => [
		'owner_guid' => $user->guid,
	],
	'resource' => 'owner',
	'page_owner' => $user,
]);

echo elgg_view_page(elgg_echo('event_manager:owner:title', [$user->getDisplayName()]), [
	'content' => $content,
	'filter_value' => $filter_value,
	'filter_id' => 'events',
]);
