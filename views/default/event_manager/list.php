<?php

elgg_load_css('fullcalendar');

$entities = elgg_extract('entities', $vars);

$options = [
	'count' => elgg_extract('count', $vars),
	'offset' => elgg_extract('offset', $vars),
	'full_view' => false,
	'pagination' => false,
	'no_results' => elgg_echo('event_manager:list:noresults'),
];

$list = elgg_view_entity_list($entities, $options);
$limit = elgg_extract('limit', $vars, 10);

if ($options['count'] > $limit) {
	$list .= elgg_format_element('div', [
		'id' => 'event_manager_event_list_search_more',
		'rel' => $options['offset'] ?: $limit
	], elgg_echo('event_manager:list:showmorevents') . ' (' . ($options['count'] - ($options['offset'] + $limit)) . ')');
}

$result = elgg_format_element('div', [
	'id' => 'event_manager_event_listing',
	'class' => 'event-manager-results',
], $list);

$result .= elgg_format_element('div', [
	'id' => 'event-manager-event-calendar',
	'class' => 'event-manager-results hidden',
]);

$result .= elgg_view('event_manager/onthemap', $vars);

echo elgg_view_module('main', '', $result);
