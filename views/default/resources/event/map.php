<?php

$title_text = elgg_echo('collection:object:event');

$page_owner = elgg_get_page_owner_entity() ?: null;
if ($page_owner instanceof \ElggGroup) {
	elgg_entity_gatekeeper($page_owner->guid, 'group');
	elgg_group_tool_gatekeeper('event_manager');
	
	$title_text = elgg_echo('event_manager:list:group:title');
} else {
	$page_owner = null;
	elgg_set_page_owner_guid(null);
}

elgg_group_tool_gatekeeper('event_manager');

elgg_push_collection_breadcrumbs('object', \Event::SUBTYPE, $page_owner);

elgg_register_title_button('event', 'add', 'object', \Event::SUBTYPE);

echo elgg_view_page($title_text, [
	'content' => elgg_view('event_manager/onthemap', $vars),
	'filter_id' => 'events',
	'filter_value' => 'map',
]);
