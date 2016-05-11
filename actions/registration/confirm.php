<?php

$event_guid = (int) get_input('event_guid');
$user_guid = (int) get_input('user_guid');
$code = get_input('code');

elgg_entity_gatekeeper($event_guid, 'object', Event::SUBTYPE);
$event = get_entity($event_guid);

elgg_entity_gatekeeper($user_guid);
$user = get_entity($user_guid);

// is the code valid
if (!event_manager_validate_registration_validation_code($event_guid, $user_guid, $code)) {
	register_error(elgg_echo('event_manager:registration:confirm:error:code'));
	forward(REFERER);
}

if (!$event->openForRegistration()) {
	register_error(elgg_echo('event_manager:event:rsvp:registration_ended'));
	forward($event->getURL());
}

// check if we can become attending or should be on the waitinglist
$relation = EVENT_MANAGER_RELATION_ATTENDING;
$slot_relation = EVENT_MANAGER_RELATION_SLOT_REGISTRATION;

if (!$event->hasEventSpotsLeft() || !$event->hasSlotSpotsLeft()) {
	if ($event->waiting_list_enabled) {
		$relation = EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST;
		$slot_relation = EVENT_MANAGER_RELATION_SLOT_REGISTRATION_WAITINGLIST;
	} else {
		register_error(elgg_echo('event_manager:event:rsvp:nospotsleft'));
		forward($event->getURL());
	}
}

// update all slots from pending to attending/waiting
$slots = $event->getRegisteredSlotsForEntity($user->getGUID(), EVENT_MANAGER_RELATION_SLOT_REGISTRATION_PENDING);
if ($slots) {
	foreach ($slots as $slot) {
		$user->removeRelationship($slot->getGUID(), EVENT_MANAGER_RELATION_SLOT_REGISTRATION_PENDING);
		$user->addRelationship($slot->getGUID(), $slot_relation);
	}
}

// update event relationsship to attending/waiting
$event->rsvp($relation, $user->getGUID());
system_message(elgg_echo('event_manager:event:relationship:message:' . $relation));

forward($event->getURL());
