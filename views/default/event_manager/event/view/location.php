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
		elgg_format_element('strong', [], elgg_echo('event_manager:edit:form:region') . ':'),
		$event_region
	);
}

if ($event_venue) {
	$location_details .= elgg_view_image_block(
		elgg_format_element('strong', [], elgg_echo('event_manager:edit:form:venue') . ':'),
		$event_venue
	);
}

if ($event_location) {
	$maps_enabled = event_manager_maps_enabled();
	
	$route = '';
	if ($maps_enabled && !empty($event->getLatitude()) && !empty($event->getLongitude())) {
		$route = elgg_view('output/url', [
			'href' => 'www.openstreetmap.org/directions?engine=osrm_car&route=%3B' . $event->getLatitude() . '%2C' . $event->getLongitude(),
			'text' => elgg_echo('event_manager:event:location:plan_route'),
			'target' => '_blank',
			'class' => 'mlm',
		]);
	}
	
	$location_details .= elgg_view_image_block(
		elgg_format_element('strong', [], elgg_echo('event_manager:edit:form:location') . ':'),
		$event_location . $route
	);
	
	if ($maps_enabled) {
		$location_details .= elgg_view('event_manager/maps/location', $vars);
	}
}

if (empty($location_details)) {
	return;
}

echo elgg_view_module('event', elgg_echo('event_manager:edit:form:tabs:location'), $location_details, [
	'class' => 'event-location',
	'id' => 'location',
]);
