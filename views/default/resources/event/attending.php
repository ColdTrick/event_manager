<?php

$user = elgg_get_page_owner_entity();

elgg_push_collection_breadcrumbs('object', 'event', $user);

$filter_value = '';
if ($user->guid === elgg_get_logged_in_user_guid()) {
	$filter_value = 'attending';
}

$list_type = get_input('list_type', 'list');

$content = elgg_view("event_manager/listing/{$list_type}", [
	'options' => [
		'metadata_name_value_pairs' => [
			[
				'name' => 'event_start',
				'value' => time(),
				'operand' => '>=',
			],
		],
		'sort_by' => [
			'property' => 'event_start',
			'direction' => 'ASC',
			'signed' => true,
		],
		'relationship' => EVENT_MANAGER_RELATION_ATTENDING,
		'relationship_guid' => $user->guid,
		'inverse_relationship' => true,
	],
	'resource' => 'attending',
	'page_owner' => $user,
]);

echo elgg_view_page(elgg_echo('event_manager:attending:title', [$user->getDisplayName()]), [
	'content' => $content,
	'filter_value' => $filter_value,
	'filter_id' => 'events',
]);
