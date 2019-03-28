<?php
/**
 * Group event manager module
 */

$group = elgg_get_page_owner_entity();

if (!$group->isToolEnabled('event_manager')) {
	return;
}

$content = elgg_list_entities([
	'type' => 'object',
	'subtype' => 'event',
	'container_guid' => elgg_get_page_owner_guid(),
	'pagination' => false,
	'limit' => 5,
]);

$all_link = elgg_view('output/url', [
	'href' => elgg_generate_url('collection:object:event:group', ['guid' => $group->guid]),
	'text' => elgg_echo('link:view:all'),
]);

$new_link = elgg_view('output/url', [
	'href' => elgg_generate_url('add:object:event', ['guid' => $group->guid]),
	'text' => elgg_echo('event:add'),
]);

echo elgg_view('groups/profile/module', [
	'title' => elgg_echo('event_manager:group'),
	'content' => $content,
	'all_link' => $all_link,
	'add_link' => $new_link,
]);
