<?php 

$tabs = [
	[
		'text' => elgg_echo('event_manager:list:navigation:list'),
		'href' => 'javascript:void(0);',
		'rel' => 'list',
		'selected' => true
	],
	[
		'text' => elgg_echo('event_manager:list:navigation:onthemap'),
		'href' => 'javascript:void(0);',
		'rel' => 'onthemap'
	]
];

$tabs = elgg_view('navigation/tabs', [
	'tabs' => $tabs,
	'type' => 'horizontal'
]);

echo elgg_format_element('div', ['id' => 'event_manager_result_navigation'], $refreshing . $tabs);
