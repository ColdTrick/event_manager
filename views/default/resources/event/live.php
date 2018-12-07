<?php

$title_text = elgg_echo('event_manager:list:live');

elgg_push_collection_breadcrumbs('object', \Event::SUBTYPE);

elgg_register_title_button('event', 'add', 'object', \Event::SUBTYPE);

$content = elgg_list_entities([
	'type' => 'object',
	'subtype' => 'event',
	'metadata_name_value_pairs' => [
		[
			'name' => 'event_start',
			'value' => time(),
			'operand' => '<=',
		],
		[
			'name' => 'event_end',
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

$body = elgg_view_layout('default', [
	'title' => $title_text,
	'content' => $content,
	'filter_id' => 'events',
	'filter_value' => 'live',
]);

echo elgg_view_page($title_text, $body);
