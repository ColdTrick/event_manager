<?php

$event_guid = (int) get_input('event');
$registration_guid = (int) get_input('registration');
$code = get_input('code');

elgg_entity_gatekeeper($event_guid, 'object', Event::SUBTYPE);
$event = get_entity($event_guid);

elgg_entity_gatekeeper($registration_guid, 'object', EventRegistration::SUBTYPE);
$registration = get_entity($registration_guid);

$verify_code = event_manager_create_unsubscribe_code($registration, $event);

if ($code !== $verify_code) {
	return elgg_error_response(elgg_echo('event_manager:unsubscribe_confirm:error:code'));
}

if (!$event->rsvp(EVENT_MANAGER_RELATION_UNDO, $registration->getGUID())) {
	return elgg_error_response(elgg_echo('event_manager:action:unsubscribe_confirm:error'));
}

return elgg_ok_response('', elgg_echo('event_manager:action:unsubscribe_confirm:success'), $event->getURL());
