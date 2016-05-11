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

// check if object has relation ship that can be moved
$user_relationship = $event->getRelationshipByUser($object->getGUID());

if (!in_array($user_relationship, [EVENT_MANAGER_RELATION_ATTENDING_PENDING, EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST])) {
	forward(REFERER);
}

// update pending slots
$slots = $event->getRegisteredSlotsForEntity($object->getGUID(), EVENT_MANAGER_RELATION_SLOT_REGISTRATION_PENDING);
if ($slots) {
	foreach ($slots as $slot) {
		$object->removeRelationship($slot->getGUID(), EVENT_MANAGER_RELATION_SLOT_REGISTRATION_PENDING);
		$object->addRelationship($slot->getGUID(), EVENT_MANAGER_RELATION_SLOT_REGISTRATION);
	}
}

// update waiting slots
$slots = $event->getRegisteredSlotsForEntity($object->getGUID(), EVENT_MANAGER_RELATION_SLOT_REGISTRATION_WAITINGLIST);
if ($slots) {
	foreach ($slots as $slot) {
		$object->removeRelationship($slot->getGUID(), EVENT_MANAGER_RELATION_SLOT_REGISTRATION_WAITINGLIST);
		$object->addRelationship($slot->getGUID(), EVENT_MANAGER_RELATION_SLOT_REGISTRATION);
	}
}

// pending / waitinglist
$event->rsvp(EVENT_MANAGER_RELATION_ATTENDING, $object->getGUID());
system_message(elgg_echo('event_manager:action:move_to_attendees:success'));

forward(REFERER);
