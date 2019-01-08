<?php

$event = elgg_extract('entity', $vars);

$datetime = elgg_view('event_manager/event/view/datetime', $vars);
$registration = elgg_view('event_manager/event/view/registration', $vars);

$body = elgg_view('event_manager/event/view/banner', $vars);
$body .= elgg_format_element('div', ['class' => 'event-manager-header'], $datetime . $registration);
$body .= elgg_view('event_manager/event/view/description', $vars);
$body .= elgg_view('event_manager/event/view/contact_details', $vars);
$body .= elgg_view('event_manager/event/view/location', $vars);
$body .= elgg_view('event_manager/program/view', $vars);
$body .= elgg_view('event_manager/event/view/files', $vars);
$body .= elgg_view('event_manager/event/view/attendees', $vars);

$params = [
	'icon' => true,
	'body' => $body,
	'show_summary' => true,
	'show_navigation' => false,
];
$params = $params + $vars;

echo elgg_view('object/elements/full', $params);
