<?php
/**
 * Form fields for event location data
 */

$region_options = event_manager_event_region_options();

$venue_label = elgg_echo('event_manager:edit:form:venue');
$venue_input = elgg_view('input/text', array(
	'name' => 'venue',
	'value' => $vars["venue"],
));

$location_label = elgg_echo('event_manager:edit:form:location');
$location_input = elgg_view('input/text', array(
	'name' => 'location',
	'id' => 'openmaps',
	'value' => $vars["location"],
	'readonly' => true
));

$region_label = '';
$region_input = '';
if ($region_options) {
	$region_label = elgg_echo('event_manager:edit:form:region');
	$region_input = elgg_view('input/dropdown', array(
		'name' => 'region',
		'value' => $vars["region"],
		'options' => $region_options,
	));
}

$contact_details_label = elgg_echo('event_manager:edit:form:contact_details');
$contact_details_input = elgg_view('input/text', array(
	'name' => 'contact_details',
	'value' => $vars["contact_details"]
));

$website_label = elgg_echo('event_manager:edit:form:website');
$website_input = elgg_view('input/url', array(
	'name' => 'website',
	'value' => $vars["website"]
));

$twitter_hash_label = elgg_echo('event_manager:edit:form:twitter_hash');
$twitter_hash_input = elgg_view('input/text', array(
	'name' => 'twitter_hash',
	'value' => $vars["twitter_hash"]
));

echo <<<HTML
	<div>
		<label>$venue_label</label>
		$venue_input
	</div>
	<div>
		<label>$location_label</label>
		$location_input
	</div>
	<div>
		<label>$region_label</label>
		$region_input
	</div>
	<div>
		<label>$contact_details_label</label>
		$contact_details_input
	</div>
	<div>
		<label>$website_label</label>
		$website_input
	</div>
	<div>
		<label>$twitter_hash_label</label>
		$twitter_hash_input
	</div>
HTML;
