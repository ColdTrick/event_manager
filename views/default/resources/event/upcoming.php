<?php

$title_text = elgg_echo('event_manager:list:title');

$event_options = event_manager_get_default_list_options();

elgg_push_collection_breadcrumbs('object', \Event::SUBTYPE);

elgg_register_title_button('event', 'add', 'object', \Event::SUBTYPE);

$content = elgg_list_entities($event_options);

$body = elgg_view_layout('default', [
	'title' => $title_text,
	'content' => $content,
	'filter_id' => 'events',
	'filter_value' => 'upcoming',
]);

echo elgg_view_page($title_text, $body);
