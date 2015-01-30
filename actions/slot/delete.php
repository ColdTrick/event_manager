<?php

$guid = (int) get_input("guid");
$success = false;

if (!empty($guid) && $eventSlot = get_entity($guid)) {
	if ($eventSlot->getSubtype() == EventSlot::SUBTYPE) {
		if ($eventSlot->delete()) {
			$success = true;
		}
	}
}

if (!$success) {
	register_error(elgg_echo("event_manager:action:slot:delete:error"));
}
