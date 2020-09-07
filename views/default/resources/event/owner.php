<?php

use Elgg\EntityNotFoundException;

$user = elgg_get_page_owner_entity();
if (!$user instanceof \ElggUser) {
	throw new EntityNotFoundException();
}

elgg_register_title_button('event', 'add', 'object', \Event::SUBTYPE);

elgg_push_collection_breadcrumbs('object', 'event', $user);

$filter_value = '';

$title = elgg_echo('event_manager:owner:title', [$user->getDisplayName()]);
if ($user->guid === elgg_get_logged_in_user_guid()) {
	$filter_value = 'mine';
}

$list_type = get_input('list_type', 'list');
$options = [
	'owner_guid' => $user->guid,
];
$content = elgg_view("event_manager/listing/{$list_type}", [
	'options' => $options,
	'resource' => 'owner',
	'page_owner' => $user,
]);

echo elgg_view_page($title, [
	'content' => $content,
	'filter_value' => $filter_value,
	'filter_id' => 'events',
]);
