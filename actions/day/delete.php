<?php

$guid = (int) get_input("guid");
$success = false;

if (!empty($guid) && ($eventDay = get_entity($guid))) {
	if ($eventDay->getSubtype() == EventDay::SUBTYPE) {
		if ($eventDay->delete()) {
			$success = true;
		}
	}
}

if (!$success) {
	register_error(elgg_echo("event_manager:action:day:delete:error"));
}
