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
	'id' => 'openmaps',
	'value' => $vars['location'],
	'readonly' => true,
]);

$region_options = event_manager_event_region_options();
if ($region_options) {
	echo elgg_view_input('select', [
		'label' => elgg_echo('event_manager:edit:form:region'),
		'name' => 'region',
		'value' => $vars['region'],
		'options' => $region_options,
	]);
}

echo elgg_view_input('text', [
	'label' => elgg_echo('event_manager:edit:form:contact_details'),
	'name' => 'contact_details',
	'value' => $vars['contact_details'],
]);

echo elgg_view_input('url', [
	'label' => elgg_echo('event_manager:edit:form:website'),
	'name' => 'website',
	'value' => $vars['website'],
]);

echo elgg_view_input('text', [
	'label' => elgg_echo('event_manager:edit:form:twitter_hash'),
	'name' => 'twitter_hash',
	'value' => $vars['twitter_hash'],
]);
