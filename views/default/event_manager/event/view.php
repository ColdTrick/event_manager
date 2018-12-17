<?php

$event = elgg_extract('entity', $vars);

$body =  elgg_view('event_manager/event/view/banner', $vars);
$body .=  elgg_view('event_manager/event/view/datetime', $vars);
$body .=  elgg_view('event_manager/event/view/description', $vars);
$body .=  elgg_view('event_manager/event/view/location', $vars);
$body .=  elgg_view('event_manager/event/view/files', $vars);
$body .=  elgg_view('event_manager/event/view/registration', $vars);
$body .= elgg_view('event_manager/event/view/attendees', $vars);
$body .= elgg_view('event_manager/program/view', $vars);

$params = [
	'icon' => true,
	'body' => $body,
	'show_summary' => true,
	'show_navigation' => false,
];
$params = $params + $vars;

echo elgg_view('object/elements/full', $params);
