<?php

$event = elgg_extract('entity', $vars);
if (!$event || !elgg_is_logged_in()) {
	return;
}

if (!$event->registration_needed) {
	return;
}

if (!$event->hasRelationship(elgg_get_logged_in_user_guid(), EVENT_MANAGER_RELATION_ATTENDING)) {
	return;
}

echo elgg_view_url(elgg_generate_url('view:object:eventregistration', ['guid' => $event->guid]), elgg_echo('event_manager:registration:viewyourregistration'));
