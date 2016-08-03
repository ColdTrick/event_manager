<?php
/**
 * Form fields for event location data
 */

$venue = elgg_extract('venue', $vars);
$location = elgg_extract('location', $vars);
$region = elgg_extract('region', $vars);

$region_options = event_manager_event_region_options();

$collapsed = true;
if (!empty($venue) || !empty($location) || (!empty($region) && $region_options)) {
	$collapsed = false;
}

$output = elgg_view_input('text', [
	'label' => elgg_echo('event_manager:edit:form:venue'),
	'name' => 'venue',
	'value' => $venue,
]);

$output .= elgg_view_input('text', [
	'label' => elgg_echo('event_manager:edit:form:location'),
	'name' => 'location',
	'value' => $location,
	'class' => 'elgg-lightbox',
	'data-colorbox-opts' => json_encode([
		'inline' => true,
		'href' => '#event-manager-edit-maps-search-container',
	]),
	'readonly' => true,
]);

$output .= '<div class="hidden">';
$output .= elgg_format_element('div', [
	'id' => 'event-manager-edit-maps-search-container',
], elgg_view('event_manager/event/maps/select_location'));
$output .= '</div>';

if ($region_options) {
	$output .= elgg_view_input('select', [
		'label' => elgg_echo('event_manager:edit:form:region'),
		'name' => 'region',
		'value' => $region,
		'options' => $region_options,
	]);
}

if (!$collapsed) {
	echo $output;
	return;
}

$toggle_button = elgg_view('input/button', [
	'class' => 'elgg-button-action',
	'value' => elgg_echo('event_manager:edit:form:tabs:location:toggle'),
	'rel' => 'toggle',
	'data-toggle-selector' => '.event-manager-edit-location-toggle',
]);

echo elgg_format_element('div', [
	'class' => 'event-manager-edit-location-toggle center',
	'data-toggle-slide' => 0,
], $toggle_button);

echo elgg_format_element('div', [
	'class' => 'hidden event-manager-edit-location-toggle',
	'data-toggle-slide' => 0,
], $output);
