<?php

$page_owner = elgg_get_page_owner_entity() ?: null;
if ($page_owner instanceof \ElggGroup) {
	elgg_entity_gatekeeper($page_owner->guid, 'group');
	elgg_group_tool_gatekeeper('event_manager');
	
	elgg_push_collection_breadcrumbs('object', \Event::SUBTYPE, $page_owner);
} else {
	$page_owner = null;
	elgg_set_page_owner_guid(0);
}

elgg_register_title_button('add', 'object', \Event::SUBTYPE);

$list_type = get_input('list_type', 'list');

$content = elgg_view("event_manager/listing/{$list_type}", [
	'options' => [
		'container_guid' => ($page_owner instanceof ElggGroup) ? $page_owner->guid : ELGG_ENTITIES_ANY_VALUE,
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
	],
	'resource' => 'upcoming',
	'page_owner' => $page_owner,
]);

echo elgg_view_page(elgg_echo('event_manager:list:upcoming'), [
	'content' => $content,
	'filter_id' => 'events',
	'filter_value' => 'upcoming',
]);
