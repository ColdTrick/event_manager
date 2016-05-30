<?php

$user = elgg_get_page_owner_entity();
if (!($user instanceof \ElggUser)) {
	forward('', '404');
}

$filter_context = '';

$title = elgg_echo('event_manager:attending:title', [$user->name]);
if ($user->guid === elgg_get_logged_in_user_guid()) {
	$filter_context = 'attending';
}

$content = elgg_list_entities_from_relationship([
	'type' => 'object',
	'subtype' => \Event::SUBTYPE,
	'relationship' => EVENT_MANAGER_RELATION_ATTENDING,
	'relationship_guid' => $user->guid,
	'inverse_relationship' => true,
	'no_results' => elgg_echo('notfound'),
]);

$body = elgg_view_layout('content', [
	'content' => $content,
	'title' => $title,
	'filter_context' => $filter_context,
]);

echo elgg_view_page($title, $body);
