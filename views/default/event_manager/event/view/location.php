<?php

$event = elgg_extract('entity', $vars);
if (!$event instanceof \Event) {
	return;
}

$maps_provider = elgg_get_plugin_setting('maps_provider', 'event_manager', 'google');

// location
$location_details = '';

$event_location = $event->location;
$event_venue = $event->venue;
$event_region = $event->region;

if ($event_region) {
	$location_details .= '<div class="event-manager-event-view-level">';
	$location_details .= '<label class="prm">' . elgg_echo('event_manager:edit:form:region') . ':</label>';
	$location_details .= '<span>' . $event_region . '</span>';
	$location_details .= '</div>';
}

if ($event_venue) {
	$location_details .= '<div class="event-manager-event-view-level">';
	$location_details .= '<label class="prm">' . elgg_echo('event_manager:edit:form:venue') . ':</label>';
	$location_details .= '<span>' . $event_venue . '</span>';
	$location_details .= '</div>';
}

if ($event_location) {
	$location_text = $event_location;
	$location_text .= elgg_view("event_manager/maps/{$maps_provider}/route", $vars);
	
	$location_details .= '<div class="event-manager-event-view-level">';
	$location_details .= '<label class="prm">' . elgg_echo('event_manager:edit:form:location') . ':</label>';
	$location_details .= '<span>' . $location_text . '</span>';
	$location_details .= '</div>';
	
	$location_details .= elgg_view("event_manager/maps/{$maps_provider}/location", $vars);
}

if (empty($location_details)) {
	return;
}

echo elgg_view_module('event', '', $location_details, ['id' => 'location']);
