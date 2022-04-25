<?php

$event = elgg_extract('entity', $vars);
if (!$event instanceof Event) {
	return;
}

if ($event->owner_guid === elgg_get_logged_in_user_guid()) {
	$vars['class'] = elgg_extract_class($vars, 'event-manager-event-owner');
} elseif ($event->getRelationshipByUser()) {
	$vars['class'] = elgg_extract_class($vars, 'event-manager-event-attending');
}

if (elgg_extract('full_view', $vars)) {
	echo elgg_view('event_manager/event/view', $vars);
	return;
}

$content = '';

$excerpt = $event->getExcerpt();
if ($excerpt) {
	$content .= elgg_format_element('div', [], $excerpt);
}

$content .= elgg_view('event_manager/event/rsvp', $vars);

$imprint = elgg_extract('imprint', $vars, []);

$location = $event->location;
if ($location) {
	$imprint['location'] = [
		'icon_name' => 'map-marker-alt',
		'content' => elgg_view_url($event->getURL() . '#location', $location),
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
