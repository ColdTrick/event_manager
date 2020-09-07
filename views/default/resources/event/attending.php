<?php

use Elgg\EntityNotFoundException;

$user = elgg_get_page_owner_entity();
if (!$user instanceof \ElggUser) {
	throw new EntityNotFoundException();
}

elgg_push_collection_breadcrumbs('object', 'event', $user, false);

$filter_value = '';

$title = elgg_echo('event_manager:attending:title', [$user->getDisplayName()]);
if ($user->guid === elgg_get_logged_in_user_guid()) {
	$filter_value = 'attending';
}

$list_type = get_input('list_type', 'list');
$options = [
	'metadata_name_value_pairs' => [
		[
			'name' => 'event_start',
			'value' => time(),
			'operand' => '>=',
		],
	],
	'order_by_metadata' => [
		'name' => 'event_start',
		'direction' => 'ASC',
		'as' => 'integer'
	],
	'relationship' => EVENT_MANAGER_RELATION_ATTENDING,
	'relationship_guid' => $user->guid,
	'inverse_relationship' => true,
];
$content = elgg_view("event_manager/listing/{$list_type}", [
	'options' => $options,
	'resource' => 'attending',
	'page_owner' => $user,
]);

echo elgg_view_page($title, [
	'content' => $content,
	'filter_value' => $filter_value,
	'filter_id' => 'events',
]);
