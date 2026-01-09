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

$content = elgg_view_form(
	'event_manager/import/ical',
	[],
	[
		'list_route' => get_input('list_route'),
		'route_parameters' => get_input('route_parameters'),
	]
);

echo elgg_view_page(elgg_echo('event_manager:ical_direct:import'), [
	'content' => $content,
	'filter_id' => 'events',
	'filter_value' => 'import-ical',
]);
