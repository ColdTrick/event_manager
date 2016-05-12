<?php

$event = elgg_extract('entity', $vars);

$options = [];

$context = elgg_get_context();
$rsvp = elgg_view("event_manager/event/rsvp", $vars);
if ($rsvp) {
	$options[] = $rsvp;
}

if (in_array($context, ['widgets', 'maps'])) {
	$attendee_count = $event->countAttendees();
	if ($attendee_count > 0 || $event->openForRegistration()) {
		$options[] = elgg_echo('event_manager:event:relationship:event_attending:entity_menu', [$attendee_count]);
	}
}

echo implode(' ', $options);
