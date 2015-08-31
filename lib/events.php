<?php
/**
 * Events are bundled here
 */

/**
 * Checks if there are new slots available after updating an event
 *
 * @param string     $event  name of the event
 * @param string     $type   type of the event
 * @param ElggEntity $object object related to the event
 *
 * @return void
 */
function event_manager_update_object_handler($event, $type, $object) {
	if (empty($object) || !($object instanceof Event)) {
		return;
	}
	
	$fillup = false;
	if ($object->with_program && $object->hasSlotSpotsLeft()) {
		$fillup = true;
	} elseif (!$object->with_program && $object->hasEventSpotsLeft()) {
		$fillup = true;
	}

	if (!$fillup) {
		return;
	}

	while ($object->generateNewAttendee()) {
		continue;
	}
}
