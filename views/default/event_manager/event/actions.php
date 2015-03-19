<?php

$event = $vars["entity"];

$options = array();

$context = elgg_get_context();

if (elgg_is_logged_in()) {
	if ($rsvp = elgg_view("event_manager/event/rsvp", $vars)) {
		$options[] = $rsvp;
	}

	if (!in_array($context, array("widgets", "maps"))) {
		if ($registration = elgg_view("event_manager/event/registration", $vars)) {
			$options[] = $registration;
		}
	}
} else {
	if ($event->register_nologin && $event->openForRegistration()) {
		$register_link = '/events/event/register/' . $event->getGUID();
		
		$register_button = elgg_view('output/url', array("class" => "elgg-button elgg-button-submit", "href" => $register_link, "text" => elgg_echo('event_manager:event:register:register_link')));

		if ($vars["full_view"]) {
			$register_button = "<div class='center'>" . $register_button . "</div>";
		}
		
		$options[] = $register_button;
	}
}

$attendee_count = $event->countAttendees();
if (($attendee_count > 0 || $event->openForRegistration()) && (elgg_in_context("widgets") || elgg_in_context("maps"))) {
	$options[] = elgg_echo("event_manager:event:relationship:event_attending:entity_menu", array($attendee_count));
}

if ($event->canEdit() && $vars["full_view"] && $event->show_attendees) {
	$waiting_count = $event->countWaiters();
	
	if ($attendee_count || $waiting_count) {
		

		// add attendee search
		$search_box = "<span class='event-manager-event-view-search-attendees' title='" . elgg_echo("event_manager:event:search_attendees") . "'>";
		$search_box .= elgg_view("input/text", array("id" => "event-manager-event-view-search-attendees","name" => "q","class" => "mrs", "autocomplete" => "off"));
		$search_box .= elgg_view_icon("search");
		$search_box .= "</span>";
		
		$options[] = $search_box;
	}
}

echo implode(" | ", $options);
