<?php

$event = elgg_extract('entity', $vars);

$output = '<div class="maps_infowindow clearfix">';
$output .= '<div class="maps_infowindow_text">';
$output .= '<div class="event_manager_event_view_owner"><a href="' . $event->getURL() . '">' . $event->getDisplayName() . '</a><br />' . event_manager_format_date($event->getStartTimestamp()) . '</div>';
$output .= str_replace(',', '<br />', $event->location) . '<br /><br />' . $event->shortdescription . '<br /><br />';
$output .= elgg_view('event_manager/event/rsvp', $vars) . '</div>';
if ($event->icontime) {
	$output .= '<div class="maps_infowindow_icon"><img src="' . $event->getIconURL() . '" /></div>';
}
$output .= '</div>';

echo $output;
