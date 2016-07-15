<?php
/**
 * Form fields for event location data
 */

echo elgg_view_input('text', [
	'label' => elgg_echo('event_manager:edit:form:venue'),
	'name' => 'venue',
	'value' => $vars['venue'],
]);

echo elgg_view_input('text', [
	'label' => elgg_echo('event_manager:edit:form:location'),
	'name' => 'location',
	'value' => $vars['location'],
	'class' => 'elgg-lightbox',
	'data-colorbox-opts' => json_encode([
		'inline' => true,
		'href' => '#event-manager-edit-maps-search-container',
	]),
	'readonly' => true,
]);

echo '<div class="hidden">';
echo elgg_format_element('div', [
	'id' => 'event-manager-edit-maps-search-container',
], elgg_view('event_manager/event/maps/select_location'));
echo '</div>';

$region_options = event_manager_event_region_options();
if ($region_options) {
	echo elgg_view_input('select', [
		'label' => elgg_echo('event_manager:edit:form:region'),
		'name' => 'region',
		'value' => $vars['region'],
		'options' => $region_options,
	]);
}
