<?php

$event = elgg_extract('entity', $vars);

$options = [];

$context = elgg_get_context();

if (elgg_is_logged_in()) {
	if ($rsvp = elgg_view("event_manager/event/rsvp", $vars)) {
		$options[] = $rsvp;
	}
} else {
	if ($event->register_nologin && $event->openForRegistration()) {
		$register_link = '/events/event/register/' . $event->getGUID();
		
		$register_button = elgg_view('output/url', [
			'class' => 'elgg-button elgg-button-submit',
			'href' => $register_link,
			'text' => elgg_echo('event_manager:event:register:register_link'),
		]);

		if ($vars['full_view']) {
			$register_button = "<div class='center'>" . $register_button . "</div>";
		}
		
		$options[] = $register_button;
	}
}

if (in_array($context, ['widgets', 'maps'])) {
	$attendee_count = $event->countAttendees();
	if ($attendee_count > 0 || $event->openForRegistration()) {
		$options[] = elgg_echo('event_manager:event:relationship:event_attending:entity_menu', [$attendee_count]);
	}
}

echo implode(' ', $options);
