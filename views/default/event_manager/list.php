<?php

$entities = elgg_extract('entities', $vars);

$result = elgg_view_menu('events_list', ['class' => 'elgg-tabs']);

$options = [
	'count' => elgg_extract('count', $vars),
	'offset' => elgg_extract('offset', $vars),
	'full_view' => false,
	'pagination' => false,
	'no_results' => elgg_echo('event_manager:list:noresults'),
];

$list = elgg_view_entity_list($entities, $options);

$result .= elgg_format_element('div', ['id' => 'event_manager_event_listing'], $list);

$result .= elgg_view('event_manager/onthemap', $vars);

$limit = elgg_extract('limit', $vars, 10);

if ($options['count'] > $limit) {
	$result .= '<div id="event_manager_event_list_search_more" rel="' . ((isset($options['offset'])) ? $options['offset'] : $limit) . '">';
	$result .= elgg_echo('event_manager:list:showmorevents');
	$result .= ' (' . ($options['count'] - ($offset + $limit)) . ')</div>';
}

echo elgg_view_module('main', '', $result);
