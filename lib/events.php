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
	if (!empty($object) && ($object instanceof Event)) {
		$fillup = false;

		if ($object->with_program && $object->hasSlotSpotsLeft()) {
			$fillup = true;
		} elseif (!$object->with_program && $object->hasEventSpotsLeft()) {
			$fillup = true;
		}

		if ($fillup) {
			while ($object->generateNewAttendee()) {
				continue;
			}
		}
	}
}

/**
 * Run upgrades
 *
 * TODO Once there are more upgrades, mark successful ones to
 * database to prevent running them more than once.
 */
function event_manager_run_upgrades() {
	$upgrade_path = elgg_get_plugins_path() . 'event_calendar/lib/upgrades/';

	$handle = opendir($upgrade_path);

	while ($upgrade_file = readdir($handle)) {
		$file_path = $upgrade_path . $upgrade_file;

		if (is_dir($file_path)) {
			continue;
		}

		include $file_path;
	}
}
