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

elgg_push_collection_breadcrumbs('object', \Event::SUBTYPE, $page_owner);

elgg_register_title_button('event', 'add', 'object', \Event::SUBTYPE);

elgg_require_js('event_manager/calendar');
elgg_load_css('fullcalendar');

$content = elgg_format_element('div', ['id' => 'event-manager-event-calendar']);

$body = elgg_view_layout('default', [
	'title' => $title_text,
	'content' => $content,
	'filter_id' => 'events',
	'filter_value' => 'calendar',
]);

echo elgg_view_page($title_text, $body);
