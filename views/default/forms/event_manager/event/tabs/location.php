<?php
/**
 * Form fields for event location data
 */

$venue = elgg_extract('venue', $vars);
$location = elgg_extract('location', $vars);
$region = elgg_extract('region', $vars);

$region_options = event_manager_event_region_options();

$output = elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('event_manager:edit:form:venue'),
	'#help' => elgg_echo('event_manager:edit:form:venue:help'),
	'name' => 'venue',
	'value' => $venue,
]);

$output .= elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('event_manager:edit:form:location'),
	'#help' => elgg_echo('event_manager:edit:form:location:help'),
	'name' => 'location',
	'value' => $location,
	'readonly' => true,
]);

$output .= '<div class="hidden">';
$output .= elgg_format_element('div', [
	'id' => 'event-manager-edit-maps-search-container',
], elgg_view('event_manager/event/maps/select_location'));
$output .= '</div>';

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
