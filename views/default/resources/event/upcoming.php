<?php

$page_owner = elgg_get_page_owner_entity() ?: null;
if ($page_owner instanceof \ElggGroup) {
	elgg_entity_gatekeeper($page_owner->guid, 'group');
	elgg_group_tool_gatekeeper('event_manager');
} else {
	$page_owner = null;
	elgg_set_page_owner_guid(null);
}

elgg_group_tool_gatekeeper('event_manager');

elgg_push_collection_breadcrumbs('object', \Event::SUBTYPE, $page_owner);

elgg_register_title_button('event', 'add', 'object', \Event::SUBTYPE);

$title_text = elgg_echo('event_manager:list:upcoming');

$content = elgg_list_entities([
	'type' => 'object',
	'subtype' => 'event',
	'container_guid' => ($page_owner instanceof ElggGroup) ? $page_owner->guid : ELGG_ENTITIES_ANY_VALUE,
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
	'no_results' => elgg_echo('event_manager:list:noresults'),
]);

echo elgg_view_page($title_text, [
	'content' => $content,
	'filter_id' => 'events',
	'filter_value' => 'upcoming',
]);
