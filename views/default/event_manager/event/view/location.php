<?php

$event = elgg_extract('entity', $vars);
if (!$event instanceof \Event) {
	return;
}

$location_details = '';

$event_location = $event->location;
$event_venue = $event->venue;
$event_region = $event->region;

if ($event_region) {
	$location_details .= elgg_view_image_block(
		elgg_format_element('label', [], elgg_echo('event_manager:edit:form:region') . ':'),
		$event_region
	);
}

if ($event_venue) {
	$location_details .= elgg_view_image_block(
		elgg_format_element('label', [], elgg_echo('event_manager:edit:form:venue') . ':'),
		$event_venue
	);
}

if ($event_location) {
	$maps_provider = event_manager_get_maps_provider();
	
	$location_details .= elgg_view_image_block(
		elgg_format_element('label', [], elgg_echo('event_manager:edit:form:location') . ':'),
		$event_location . elgg_view("event_manager/maps/{$maps_provider}/route", $vars)
	);
	
	$location_details .= elgg_view("event_manager/maps/{$maps_provider}/location", $vars);
}

if (empty($location_details)) {
	return;
}

echo elgg_view_module('event', '', $location_details, ['id' => 'location']);
