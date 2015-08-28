<?php

$event = elgg_extract('entity', $vars);
if (!$event) {
	return;
}

if (!$event->registration_needed) {
	return;
}

if (!$event->isAttending()) {
	return;
}

echo elgg_view('output/url', [
	'href' => '/events/registration/view/' . $event->getGUID(), 
	'text' => elgg_echo('event_manager:registration:viewyourregistration')
]);
