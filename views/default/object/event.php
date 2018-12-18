<?php

$event = elgg_extract('entity', $vars);
if (!$event instanceof Event) {
	return;
}

if (elgg_extract('full_view', $vars)) {
	echo elgg_view("event_manager/event/view", $vars);
	return;
}

$content = '';

$excerpt = $event->getExcerpt();
if ($excerpt) {
	$content .= '<div>' . $excerpt . '</div>';
}

$content .= elgg_view('event_manager/event/rsvp', $vars);

$imprint = elgg_extract('imprint', $vars, []);

$location = $event->location;
if ($location) {
	$imprint['location'] = [
		'icon_name' => 'map-marker-alt',
		'content' => elgg_view('output/url', [
			'href' => $event->getURL() . '#location',
			'text' => $location,
		]),
	];
}

$attendee_count = $event->countAttendees();
if ($attendee_count > 0 || $event->openForRegistration()) {
	$imprint['attendee_count'] = [
		'icon_name' => 'users',
		'content' => elgg_echo('event_manager:event:relationship:event_attending:entity_menu', [$attendee_count]),
	];
}

$params = [
	'entity' => $event,
	'content' => $content,
	'imprint' => $imprint,
	'time' => false,
	'icon' => elgg_view_entity_icon($event, 'date'),
];
$params = $params + $vars;

echo elgg_view('object/elements/summary', $params);
