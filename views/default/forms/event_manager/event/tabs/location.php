<?php
/**
 * Form fields for event location data
 */

$venue = elgg_extract('venue', $vars);
$location = elgg_extract('location', $vars);
$region = elgg_extract('region', $vars);

$region_options = event_manager_event_region_options();

$maps_provider = elgg_get_plugin_setting('maps_provider', 'event_manager', 'google');

$output = elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('event_manager:edit:form:venue'),
	'#help' => elgg_echo('event_manager:edit:form:venue:help'),
	'name' => 'venue',
	'value' => $venue,
]);

$field_options = [
	'#type' => 'text',
	'#label' => elgg_echo('event_manager:edit:form:location'),
	'#help' => elgg_echo('event_manager:edit:form:location:help'),
	'name' => 'location',
	'value' => $location,
];

if (elgg_view_exists("event_manager/maps/{$maps_provider}/location_input")) {
	$field_options['data-has-maps'] = true;
	
	$params = $vars;
	$params['field_options'] = $field_options;
	$output .= elgg_view("event_manager/maps/{$maps_provider}/location_input", $params);
	$output .= elgg_view('forms/event_manager/event/tabs/location_search', $vars);
} else {
	$output .= elgg_view_field($field_options);
}

if ($region_options) {
	$output .= elgg_view_field([
		'#type' => 'select',
		'#label' => elgg_echo('event_manager:edit:form:region'),
		'#help' => elgg_echo('event_manager:edit:form:region:help'),
		'name' => 'region',
		'value' => $region,
		'options' => $region_options,
	]);
}

echo $output;
