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
	$body .= elgg_view_comments($event);
}

$entity_menu = elgg_view_menu('entity', [
	'entity' => $event,
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
	'handler' => 'event',
]);

$params = [
	'entity' => $event,
	'title' => false,
	'tags' => false,
	'metadata' => $entity_menu,
	'subtitle' => elgg_view('page/elements/by_line', $vars),
];
$params = $params + $vars;
$summary = elgg_view('object/elements/summary', $params);

echo elgg_view('object/elements/full', [
	'entity' => $event,
	'summary' => $summary,
	'body' => $body,
]);
