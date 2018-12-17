<?php

$event = elgg_extract('entity', $vars);

$body =  elgg_view('event_manager/event/fields', $vars);

if ($event->show_attendees || $event->canEdit()) {
	$body .= elgg_view('event_manager/event/attendees', $vars);
}

if ($event->with_program) {
	$body .= elgg_view('event_manager/program/view', $vars);
}

$params = [
	'icon' => true,
	'body' => $body,
	'show_summary' => true,
	'show_navigation' => false,
];
$params = $params + $vars;

echo elgg_view('object/elements/full', $params);
