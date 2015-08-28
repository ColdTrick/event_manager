<?php

$result = elgg_view('event_manager/event_sort_menu');

$options = [
	'count' => elgg_extract('count', $vars),
	'offset' => elgg_extract('offset', $vars),
	'full_view' => false,
	'pagination' => false
];

$list = elgg_view_entity_list($vars['entities'], $options);

if (empty($list)) {
	$list = elgg_echo('event_manager:list:noresults');
}
$result .= elgg_format_element('div', ['id' => 'event_manager_event_listing'], $list);

$result .= elgg_view('event_manager/onthemap', $vars);

$limit = elgg_extract('limit', $vars, 10);

if ($options['count'] > $limit) {
	$result .= '<div id="event_manager_event_list_search_more" rel="' . ((isset($options['offset'])) ? $options['offset'] : $limit) . '">';
	$result .= elgg_echo('event_manager:list:showmorevents');
	$result .= ' (' . ($options['count'] - ($offset + $limit)) . ')</div>';
}

echo elgg_view_module('main', '', $result);
