<?php

$event = elgg_extract('entity', $vars);

$body =  elgg_view('event_manager/event/fields', $vars);

if ($event->show_attendees || $event->canEdit()) {
	$body .= elgg_view('event_manager/event/attendees', $vars);
}

if ($event->with_program) {
	$body .= elgg_view('event_manager/program/view', $vars);
}

if ($event->comments_on) {
}

echo elgg_view('object/elements/full', [
	'entity' => $event,
	'body' => $body,
]);
