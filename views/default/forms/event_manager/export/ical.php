<?php

elgg_import_esm('forms/event_manager/export/ical');

$list_route = get_input('list_route');
$route_parameters = get_input('route_parameters');

$calendar_type_selected = 'all';
if ($list_route == 'collection:object:event:owner') {
	$calendar_type_selected = 'owner';
} elseif (array_key_exists('guid', $route_parameters) && $route_parameters['guid'] != '') {
	$calendar_type_selected = 'group';
}

$calendar_type_options = [
	'all' => [
		'text' => elgg_echo('event_manager:ical_direct:calendar_type:all'),
		'selected' => $calendar_type_selected == 'all',
		'value' => 'all',
	],
	'group' => [
		'text' => elgg_echo('event_manager:ical_direct:calendar_type:group'),
		'selected' => $calendar_type_selected == 'group',
		'value' => 'group',
	],
	'owner' => [
		'text' => elgg_echo('event_manager:ical_direct:calendar_type:owner'),
		'selected' => $calendar_type_selected == 'owner',
		'value' => 'owner',
	],
];

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('event_manager:ical_direct:export:calendar_type'),
	'#help' => elgg_echo('event_manager:ical_direct:export:calendar_type:help'),
	'required' => true,
	'name' => 'calendar_type',
	'options_values' => $calendar_type_options,
]);

$owner = '';

if (array_key_exists('username', $route_parameters)) {
	$owner = elgg_get_user_by_username($route_parameters['username'])->getGUID();
}

echo elgg_view_field([
	'#type' => 'userpicker',
	'#label' => elgg_echo('event_manager:ical_direct:export:owner'),
	'#help' => elgg_echo('event_manager:ical_direct:export:owner:help'),
	'required' => true,
	'name' => 'owner',
	'limit' => 1,
	'values' => [$owner],
]);


$group = '';

if (array_key_exists('guid', $route_parameters)) {
	$group = $route_parameters['guid'];
}

echo elgg_view_field([
	'#type' => 'grouppicker',
	'#label' => elgg_echo('event_manager:ical_direct:export:group'),
	'#help' => elgg_echo('event_manager:ical_direct:export:group:help'),
	'required' => true,
	'name' => 'group',
	'limit' => 1,
	'values' => [$group],
]);

// Filter timespan

echo elgg_view_field([
	'#type' => 'fieldset',
	'legend' => elgg_echo('event_manager:ical_direct:export:timespan'),
	'fields' => [
		[
			'#type' => 'date',
			'#label' => elgg_echo('event_manager:ical_direct:export:start'),
			'required' => true,
			'name' => 'start_date',
			'value' => time(),
		],
		[
			'#type' => 'date',
			'#label' => elgg_echo('event_manager:ical_direct:export:end'),
			'required' => true,
			'name' => 'end_date',
			'value' => DateTime::createFromFormat('U', time())->add(DateInterval::createFromDateString('1 month'))->getTimestamp()
		]
	]
]);

// Filter region

$region_settings = trim((string) elgg_get_plugin_setting('region_list', 'event_manager'));
$region_list = explode(',', $region_settings);
$region_options = array_reduce($region_list, function ($options, $value){
	$options[$value] = [
		'text' => $value
	];
	return $options;
}, []);
echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('event_manager:ical_direct:export:region'),
	'#help' => elgg_echo('event_manager:ical_direct:export:region:help'),
	'required' => false,
	'multiple' => true,
	'name' => 'region',
	'options_values' => $region_options,
]);

// Filter type

$type_settings = trim((string) elgg_get_plugin_setting('type_list', 'event_manager'));
$type_list = explode(',', $type_settings);
$type_options = array_reduce($type_list, function ($options, $value){
	$options[$value] = [
		'text' => $value
	];
	return $options;
}, []);
echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('event_manager:ical_direct:export:type'),
	'#help' => elgg_echo('event_manager:ical_direct:export:type:help'),
	'required' => false,
	'multiple' => true,
	'name' => 'event_type',
	'options_values' => $type_options,
]);

elgg_set_form_footer(
	elgg_view_field([
		'#type' => 'submit',
		'text' => elgg_echo('event_manager:ical_direct:export:submit'),
	])
);
