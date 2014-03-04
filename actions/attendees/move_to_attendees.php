<?php

// get input
$guid = (int) get_input("guid");
$user = (int) get_input("user"); // could also be a registration object


if (!empty($guid) && !empty($user)) {
	$object = get_entity($user);
	$event = get_entity($guid);

	if (elgg_instanceof($object) && elgg_instanceof($event, "object", "event")) {
		// check if can edit event
		if ($event->canEdit()) {
			// check if object has relation ship that can be moved
			$user_relationship = $event->getRelationshipByUser($object->getGUID());
			if (in_array($user_relationship, array(EVENT_MANAGER_RELATION_ATTENDING_PENDING, EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST))) {
				
				// update pending slots
				$slots = $event->getRegisteredSlotsForEntity($object->getGUID(), EVENT_MANAGER_RELATION_SLOT_REGISTRATION_PENDING);
				if ($slots) {
					foreach($slots as $slot) {
						$object->removeRelationship($slot->getGUID(), EVENT_MANAGER_RELATION_SLOT_REGISTRATION_PENDING);
						$object->addRelationship($slot->getGUID(), EVENT_MANAGER_RELATION_SLOT_REGISTRATION);
					}
				}

				// update waiting slots
				$slots = $event->getRegisteredSlotsForEntity($object->getGUID(), EVENT_MANAGER_RELATION_SLOT_REGISTRATION_WAITINGLIST);
				if ($slots) {
					foreach($slots as $slot) {
						$object->removeRelationship($slot->getGUID(), EVENT_MANAGER_RELATION_SLOT_REGISTRATION_WAITINGLIST);
						$object->addRelationship($slot->getGUID(), EVENT_MANAGER_RELATION_SLOT_REGISTRATION);
					}
				}
				
				// pending / waitinglist
				$event->rsvp(EVENT_MANAGER_RELATION_ATTENDING, $object->getGUID());
				system_message(elgg_echo("event_manager:action:move_to_attendees:success"));
			}
		}
	} else {
		register_error(elgg_echo("InvalidParameterException:NoEntityFound"));
	}
} else {
	register_error(elgg_echo("InvalidParameterException:MissingParameter"));
}

forward(REFERER);
