<?php
/**
 * Group event manager module
 */

$group = elgg_extract('entity', $vars);
if (!$group instanceof \ElggGroup) {
	return;
}

$content = elgg_list_entities([
	'type' => 'object',
	'subtype' => 'event',
	'container_guid' => $group->guid,
	'pagination' => false,
	'limit' => 5,
	'no_results' => true,
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
