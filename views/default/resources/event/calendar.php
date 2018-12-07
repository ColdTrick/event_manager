<?php

elgg_require_js('event_manager/calendar');
elgg_load_css('fullcalendar');

$title_text = elgg_echo('collection:object:event');

$event_options = [];

$page_owner = elgg_get_page_owner_entity() ?: null;
if ($page_owner instanceof \ElggGroup) {
	elgg_entity_gatekeeper($page_owner->guid);
	elgg_group_tool_gatekeeper('event_manager');
	$title_text = elgg_echo('event_manager:list:group:title');
}

elgg_push_collection_breadcrumbs('object', 'event', $page_owner);

elgg_register_title_button('event', 'add', 'object', 'event');

$content = elgg_format_element('div', ['id' => 'event-manager-event-calendar']);

$body = elgg_view_layout('default', [
	'filter_id' => 'events',
	'content' => $content,
	'title' => $title_text,
]);

echo elgg_view_page($title_text, $body);
