<?php

$event_guid = (int) get_input('event');
$registration_guid = (int) get_input('registration');
$code = get_input('code');

$event = get_entity($event_guid);
$registration = get_entity($registration_guid);
if (!$event instanceof \Event || !$registration instanceof \EventRegistration) {
	return elgg_echo('actionunauthorized');
}

$verify_code = event_manager_create_unsubscribe_code($registration, $event);

if ($code !== $verify_code) {
	return elgg_error_response(elgg_echo('event_manager:unsubscribe_confirm:error:code'));
}

if (!$event->rsvp(EVENT_MANAGER_RELATION_UNDO, $registration->guid)) {
	return elgg_error_response(elgg_echo('event_manager:action:unsubscribe_confirm:error'));
}

return elgg_ok_response('', elgg_echo('event_manager:action:unsubscribe_confirm:success'), $event->getURL());
