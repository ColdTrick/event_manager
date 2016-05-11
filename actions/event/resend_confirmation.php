<?php

$guid = (int) get_input('guid');
$user = (int) get_input('user'); // could also be a registration object

elgg_entity_gatekeeper($guid, 'object', Event::SUBTYPE);
$event = get_entity($guid);

elgg_entity_gatekeeper($user);
$object = get_entity($user);

if (!$event->canEdit()) {
	register_error(elgg_echo('actionunauthorized'));
	forward(REFERER);
}

event_manager_send_registration_validation_email($event, $object);
system_message(elgg_echo('event_manager:action:resend_confirmation:success'));

forward(REFERER);
