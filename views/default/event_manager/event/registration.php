<?php

$event = elgg_extract('entity', $vars);
if (!$event || !elgg_is_logged_in()) {
	return;
}

if (!$event->registration_needed) {
	return;
}

if (!check_entity_relationship($event->getGUID(), EVENT_MANAGER_RELATION_ATTENDING, elgg_get_logged_in_user_guid())) {
	return;
}

echo elgg_view('output/url', [
	'href' => '/events/registration/view/' . $event->getGUID(),
	'text' => elgg_echo('event_manager:registration:viewyourregistration')
]);
