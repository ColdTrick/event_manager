<?php

$guid = (int) get_input('guid');
$user = (int) get_input('user'); // could also be a registration object

$event = get_entity($guid);
$object = get_entity($user);

if (!$event instanceof \Event || !$event->canEdit() || (!$object instanceof \ElggUser && !$object instanceof \EventRegistration)) {
	return elgg_error_response(elgg_echo('actionunauthorized'));
}

event_manager_send_registration_validation_email($event, $object);

return elgg_ok_response('', elgg_echo('event_manager:action:resend_confirmation:success'));
