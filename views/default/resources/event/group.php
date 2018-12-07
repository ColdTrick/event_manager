<?php

$group_guid = elgg_extract('guid', $vars);
elgg_entity_gatekeeper($group_guid, 'group');

/* @var $group ElggGroup */
$group = get_entity($group_guid);

elgg_group_tool_gatekeeper('event_manager');

elgg_register_title_button('event', 'add', 'object', \Event::SUBTYPE);

elgg_push_collection_breadcrumbs('object', 'event', $group);

$title = elgg_echo('event_manager:list:group:title');

$event_options = event_manager_get_default_list_options([
	'container_guid' => $group->guid,
]);

$content = elgg_list_entities($event_options);

$body = elgg_view_layout('default', [
	'title' => $title,
	'content' => $content,
	'filter_id' => 'events/group',
	'filter_value' => 'upcomming',
]);

echo elgg_view_page($title, $body);
