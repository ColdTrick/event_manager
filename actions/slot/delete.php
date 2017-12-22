<?php

$guid = (int) get_input("guid");
$success = false;

if (!empty($guid) && $eventSlot = get_entity($guid)) {
	if ($eventSlot->getSubtype() == \ColdTrick\EventManager\Event\Slot::SUBTYPE) {
		if ($eventSlot->delete()) {
			return elgg_ok_response();
		}
	}
}

return elgg_error_response(elgg_echo('event_manager:action:slot:delete:error'));
