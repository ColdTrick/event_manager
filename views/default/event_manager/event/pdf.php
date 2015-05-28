<?php

$event = $vars["entity"];
$owner = $event->getOwnerEntity();

$output = "";

if ($event->icontime) {
	$output .= '<div class="event_manager_event_view_image"><img src="' . $event->getIconURL() . '" border="0" /></div>';
}

$output .= '<div class="event_manager_event_view_owner">';
$output .= elgg_echo('event_manager:event:view:createdby');
$output .= '</span> <a class="user" href="' . $owner->getURL() . '">' . $owner->name . '</a> ';
$output .= date(EVENT_MANAGER_FORMAT_DATE_EVENTDAY, $event->time_created);
$output .= '</div>';

// event details
$event_details = "<table>";
if ($location = $event->getEventLocation()) {
	$event_details .= '<tr><td><b>' . elgg_echo('event_manager:edit:form:location') . '</b></td><td>: ';
	$event_details .= $event->getEventLocation();
	$event_details .= '</td></tr>';
}

$event_details .= '<tr><td><b>' . elgg_echo('event_manager:edit:form:start_day') . '</b></td><td>: ' . date(EVENT_MANAGER_FORMAT_DATE_EVENTDAY, $event->start_time) . '</td></tr>';

if ($organizer = $event->organizer) {
	$event_details .= '<tr><td><b>' . elgg_echo('event_manager:edit:form:organizer') . '</b></td><td>: ' . $organizer . '</td></tr>';
}

if ($description = $event->description) {
	$event_details .= '<tr><td><b>' . elgg_echo('description') . '</b></td><td>: ' . $description . '</td></tr>';
}

if ($region = $event->region) {
	$event_details .= '<tr><td><b>' . elgg_echo('event_manager:edit:form:region') . '</b></td><td>: ' . $region . '</td></tr>';
}

if ($type = $event->event_type) {
	$event_details .= '<tr><td><b>' . elgg_echo('event_manager:edit:form:type') . '</b></td><td>: ' . $type . '</td></tr>';
}

$event_details .= "</table>";

$output .= $event_details;

echo $output;
