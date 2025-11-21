<?php

$guid = (int) get_input('guid');
$user = (int) get_input('user'); // could also be a registration object

$event = get_entity($guid);
$object = get_entity($user);

if (!$event instanceof \Event || !$event->canEdit() || (!$object instanceof \ElggUser && !$object instanceof \EventRegistration)) {
	return elgg_error_response(elgg_echo('actionunauthorized'));
}

// check if object has relationship that can be moved
$user_relationship = $event->getRelationshipByUser($object->guid);

if (!in_array($user_relationship, [EVENT_MANAGER_RELATION_ATTENDING_PENDING, EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST])) {
	return elgg_error_response();
}

// update pending slots
$slots = $event->getRegisteredSlotsForEntity($object->guid, EVENT_MANAGER_RELATION_SLOT_REGISTRATION_PENDING);
if ($slots) {
	foreach ($slots as $slot) {
		$object->removeRelationship($slot->guid, EVENT_MANAGER_RELATION_SLOT_REGISTRATION_PENDING);
		$object->addRelationship($slot->guid, EVENT_MANAGER_RELATION_SLOT_REGISTRATION);
	}
}

// update waiting slots
$slots = $event->getRegisteredSlotsForEntity($object->guid, EVENT_MANAGER_RELATION_SLOT_REGISTRATION_WAITINGLIST);
if ($slots) {
	foreach ($slots as $slot) {
		$object->removeRelationship($slot->guid, EVENT_MANAGER_RELATION_SLOT_REGISTRATION_WAITINGLIST);
		$object->addRelationship($slot->guid, EVENT_MANAGER_RELATION_SLOT_REGISTRATION);
	}
}

// pending / waitinglist
$event->rsvp(EVENT_MANAGER_RELATION_ATTENDING, $object->guid);

return elgg_ok_response('', elgg_echo('event_manager:action:move_to_attendees:success'));
