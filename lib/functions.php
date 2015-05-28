<?php
/**
 * Functions for Event Manager
 */

/**
 * Returns all relationship options
 *
 * @return array
 */
function event_manager_event_get_relationship_options() {
	return array(
		EVENT_MANAGER_RELATION_ATTENDING,
		EVENT_MANAGER_RELATION_INTERESTED,
		EVENT_MANAGER_RELATION_PRESENTING,
		EVENT_MANAGER_RELATION_EXHIBITING,
		EVENT_MANAGER_RELATION_ORGANIZING,
		EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST,
		EVENT_MANAGER_RELATION_ATTENDING_PENDING
	);
}

/**
 * Search for events
 *
 * @param array $options search options
 *
 * @return array
 */
function event_manager_search_events($options = array()) {
	$defaults = array(
		'past_events' => false,
		'count' => false,
		'container_guid' => null,
		'query' => false,
		'meattending' => false,
		'owning' => false,
		'friendsattending' => false,
		'region' => null,
		'latitude' => null,
		'longitude' => null,
		'distance' => null,
		'event_type' => false,
		'past_events' => false,
		'search_type' => "list",
		'user_guid' => elgg_get_logged_in_user_guid()
	);

	$options = array_merge($defaults, $options);

	$entities_options = array(
		'type' => 'object',
		'subtype' => 'event',
		'offset' => $options['offset'],
		'limit' => $options['limit'],
		'joins' => array(),
		'wheres' => array(),
		'order_by_metadata' => array("name" => 'start_time', "direction" => 'ASC', "as" => "integer")
	);

	if ($options["container_guid"]) {
		// limit for a group
		$entities_options['container_guid'] = $options['container_guid'];
	}

	if ($options['query']) {
		$entities_options["joins"][] = "JOIN " . elgg_get_config("dbprefix") . "objects_entity oe ON e.guid = oe.guid";
		$entities_options['wheres'][] = event_manager_search_get_where_sql('oe', array('title', 'description'), $options);
	}

	if (!empty($options['start_time'])) {
		$entities_options['metadata_name_value_pairs'][] = array('name' => 'start_time', 'value' => $options['start_time'], 'operand' => '>=');
	}

	if (!empty($options['end_time'])) {
		$entities_options['metadata_name_value_pairs'][] = array('name' => 'end_time', 'value' => $options['end_day'], 'operand' => '<=');
	}

	if (!$options['past_events']) {
		// only show from current day or newer
		$entities_options['metadata_name_value_pairs'][] = array('name' => 'start_time', 'value' => mktime(0, 0, 1), 'operand' => '>=');
	}

	if ($options['meattending'] && !empty($options["user_guid"])) {
		$entities_options['joins'][] = "JOIN " . elgg_get_config("dbprefix") . "entity_relationships e_r ON e.guid = e_r.guid_one";

		$entities_options['wheres'][] = "e_r.guid_two = " . $options["user_guid"];
		$entities_options['wheres'][] = "e_r.relationship = '" . EVENT_MANAGER_RELATION_ATTENDING . "'";
	}

	if ($options['owning'] && !empty($options["user_guid"])) {
		$entities_options['owner_guids'] = array($options["user_guid"]);
	}

	if ($options["region"]) {
		$entities_options['metadata_name_value_pairs'][] = array('name' => 'region', 'value' => $options["region"]);
	}

	if ($options["event_type"]) {
		$entities_options['metadata_name_value_pairs'][] = array('name' => 'event_type', 'value' => $options["event_type"]);
	}

	if ($options['friendsattending'] && !empty($options["user_guid"])) {
		$friends_guids = array();
		$user = get_entity($options["user_guid"]);

		if ($friends = $user->getFriends("", false)) {
			foreach ($friends as $friend) {
				$friends_guids[] = $friend->getGUID();
			}
			$entities_options['joins'][] = "JOIN " . elgg_get_config("dbprefix") . "entity_relationships e_ra ON e.guid = e_ra.guid_one";
			$entities_options['wheres'][] = "(e_ra.guid_two IN (" . implode(", ", $friends_guids) . "))";
		} else {
			// return no result
			$entities_options['joins'] = array();
			$entities_options['wheres'] = array("(1=0)");
		}
	}

	if (($options["search_type"] == "onthemap") && !empty($options['latitude']) && !empty($options['longitude']) && !empty($options['distance'])) {
		$entities_options["latitude"] = $options['latitude'];
		$entities_options["longitude"] = $options['longitude'];
		$entities_options["distance"] = $options['distance'];
		$entities = elgg_get_entities_from_location($entities_options);

		$entities_options['count'] = true;
		$count_entities = elgg_get_entities_from_location($entities_options);

	} else {

		$entities = elgg_get_entities_from_metadata($entities_options);

		$entities_options['count'] = true;
		$count_entities = elgg_get_entities_from_metadata($entities_options);
	}

	$result = array(
		"entities" => $entities,
		"count" => $count_entities
	);

	return $result;
}

/**
 * Export the event attendees. Returns csv body
 *
 * @param ElggObject $event the event
 * @param string     $rel   relationship type
 *
 * @return string
 */
function event_manager_export_attendees($event, $rel = EVENT_MANAGER_RELATION_ATTENDING) {
	$old_ia = elgg_set_ignore_access(true);

	$headerString = "";
	$dataString = "";

	$headerString .= '"' . elgg_echo('guid') . '";"' . elgg_echo('name') . '";"' . elgg_echo('email') . '";"' . elgg_echo('username') . '";"' . elgg_echo('registration date') . '"';

	if ($event->registration_needed) {
		if ($registration_form = $event->getRegistrationFormQuestions()) {
			foreach ($registration_form as $question) {
				$headerString .= ';"' . $question->title . '"';
			}
		}
	}

	if ($event->with_program) {
		if ($eventDays = $event->getEventDays()) {
			foreach ($eventDays as $eventDay) {
				$date = date(EVENT_MANAGER_FORMAT_DATE_EVENTDAY, $eventDay->date);
				if ($eventSlots = $eventDay->getEventSlots()) {
					foreach ($eventSlots as $eventSlot) {
						$start_time = $eventSlot->start_time;
						$end_time = $eventSlot->end_time;

						$start_time_hour = date('H', $start_time);
						$start_time_minutes = date('i', $start_time);

						$end_time_hour = date('H', $end_time);
						$end_time_minutes = date('i', $end_time);

						$headerString .= ';"Event activity: \'' . addslashes($eventSlot->title) . '\' ' . $date . ' (' . $start_time_hour . ':' . $start_time_minutes . ' - ' . $end_time_hour . ':' . $end_time_minutes . ')"';
					}
				}
			}
		}
	}

	$attendees = $event->exportAttendees($rel);
	if ($attendees) {
		foreach ($attendees as $attendee) {
			$answerString = '';

			$dataString .= '"' . $attendee->guid . '";"' . $attendee->name . '";"' . $attendee->email . '";"' . $attendee->username . '"';

			$relation = check_entity_relationship($event->guid, $rel, $attendee->guid);
			$dataString .= ';"' . date("d-m-Y H:i:s", $relation->time_created) . '"';

			if ($event->registration_needed) {
				if ($registration_form = $event->getRegistrationFormQuestions()) {
					foreach ($registration_form as $question) {
						$answer = $question->getAnswerFromUser($attendee->getGUID());

						$answerString .= '"' . addslashes($answer->value) . '";';
					}
				}
				$dataString .= ';' . substr($answerString, 0, (strlen($answerString) - 1));
			}

			if ($event->with_program) {
				if ($eventDays = $event->getEventDays()) {
					foreach ($eventDays as $eventDay) {
						if ($eventSlots = $eventDay->getEventSlots()) {
							foreach ($eventSlots as $eventSlot) {
								if (check_entity_relationship($attendee->getGUID(), EVENT_MANAGER_RELATION_SLOT_REGISTRATION, $eventSlot->getGUID())) {
									$dataString .= ';"V"';
								} else {
									$dataString .= ';""';
								}
							}
						}
					}
				}
			}

			$dataString .= PHP_EOL;
		}
	}

	elgg_set_ignore_access($old_ia);

	return $headerString . PHP_EOL . $dataString;
}

/**
 * Sanitizes file name
 *
 * @param string $string          file name
 * @param bool   $force_lowercase forces file name to lower case
 * @param bool   $anal            only return alfanumeric characters
 *
 * @return string
 */
function event_manager_sanitize_filename($string, $force_lowercase = true, $anal = false) {
	$strip = array(
		"~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
		"}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
		"—", "–", ",", "<", ">", "/", "?"
	);
	$clean = trim(str_replace($strip, "", strip_tags($string)));
	$clean = preg_replace('/\s+/', "-", $clean);
	$clean = ($anal) ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean ;

	return ($force_lowercase) ?
		(function_exists('mb_strtolower')) ?
			mb_strtolower($clean, 'UTF-8') :
			strtolower($clean) :
		$clean;
}

/**
 * Returns the where part for a event search sql query
 *
 * @param string $table  table prefix
 * @param array  $fields fields to search
 * @param array  $params parameters to search
 *
 * @return string
 */
function event_manager_search_get_where_sql($table, $fields, $params) {

	// TODO: why not use a search hook?
	$query = $params['query'];

	// add the table prefix to the fields
	foreach ($fields as $i => $field) {
		if ($table) {
			$fields[$i] = "$table.$field";
		}
	}

	$likes = array();
	$query = sanitise_string($query);
	foreach ($fields as $field) {
		$likes[] = "$field LIKE '%$query%'";
	}
	$likes_str = implode(' OR ', $likes);

	return "($likes_str)";
}

/**
 * Returns event region options
 *
 * @return bool|array
 */
function event_manager_event_region_options() {
	$result = false;

	$region_settings = trim(elgg_get_plugin_setting('region_list', 'event_manager'));

	if (!empty($region_settings)) {
		$region_options = array('-');
		$region_list = explode(',', $region_settings);
		$region_options = array_merge($region_options, $region_list);

		array_walk($region_options, create_function('&$val', '$val = trim($val);'));

		$result = $region_options;
	}

	return $result;
}

/**
 * Returns event type options
 *
 * @return bool|array
 */
function event_manager_event_type_options() {
	$result = false;

	$type_settings = trim(elgg_get_plugin_setting('type_list', 'event_manager'));

	if (!empty($type_settings)) {
		$type_options = array('-');
		$type_list = explode(',', $type_settings);
		$type_options = array_merge($type_options, $type_list);

		array_walk($type_options, create_function('&$val', '$val = trim($val);'));

		$result = $type_options;
	}

	return $result;
}

/**
 * Pad time
 *
 * @param string &$value current value to be padded
 *
 * @return void
 */
function event_manager_time_pad(&$value) {
	$value = str_pad($value, 2, "0", STR_PAD_LEFT);
}

/**
 * Creates an unsubscribe code
 *
 * @param EventRegistration $registration registration object
 * @param Event             $event        event
 *
 * @return false|string
 */
function event_manager_create_unsubscribe_code(EventRegistration $registration, Event $event = null) {
	$result = false;

	if (!empty($registration) && elgg_instanceof($registration, "object", EventRegistration::SUBTYPE)) {
		if (empty($event) || !elgg_instanceof($event, "object", Event::SUBTYPE)) {
			$event = $registration->getOwnerEntity();
		}

		$site_secret = get_site_secret();

		$result = md5($registration->getGUID() . $site_secret . $event->time_created);
	}

	return $result;
}

/**
 * Returns registration validation url
 *
 * @param string $event_guid guid of event
 * @param string $user_guid  guid of user
 *
 * @return false|string
 */
function event_manager_get_registration_validation_url($event_guid, $user_guid) {
	$result = false;

	if (!empty($event_guid) && !empty($user_guid)) {
		$code = event_manager_generate_registration_validation_code($event_guid, $user_guid);

		if (!empty($code)) {
			$result = "events/registration/confirm/" . $event_guid . "?user_guid=" . $user_guid . "&code=" . $code;
			$result = elgg_normalize_url($result);
		}
	}

	return $result;
}

/**
 * Returns registration validation code
 *
 * @param string $event_guid guid of event
 * @param string $user_guid  guid of user
 *
 * @return false|string
 */
function event_manager_generate_registration_validation_code($event_guid, $user_guid) {
	$result = false;

	if (!empty($event_guid) && !empty($user_guid)) {
		$event = get_entity($event_guid);
		$user = get_entity($user_guid);

		if (!empty($event) && elgg_instanceof($event, "object", Event::SUBTYPE) && !empty($user) && (elgg_instanceof($user, "user") || elgg_instanceof($user, "object", EventRegistration::SUBTYPE))) {
			$site_secret = elgg_get_config("site_secret");
			$time_created = $event->time_created;

			$result = md5($event_guid . $site_secret . $user_guid . $time_created);
		}
	}

	return $result;
}

/**
 * Validates registration validation code
 *
 * @param string $event_guid guid of event
 * @param string $user_guid  guid of user
 * @param string $code       code to validate
 *
 * @return bool
 */
function event_manager_validate_registration_validation_code($event_guid, $user_guid, $code) {
	$result = false;

	if (!empty($event_guid) && !empty($user_guid) && !empty($code)) {
		$valid_code = event_manager_generate_registration_validation_code($event_guid, $user_guid);

		if (!empty($valid_code)) {
			if ($code == $valid_code) {
				$result = true;
			}
		}
	}

	return $result;
}

/**
 * Send registration validation email
 *
 * @param Event      $event  event
 * @param ElggEntity $entity object or user to send mail to
 *
 * @return void
 */
function event_manager_send_registration_validation_email(Event $event, ElggEntity $entity) {
	$subject = elgg_echo("event_manager:registration:confirm:subject", array($event->title));
	$message = elgg_echo("event_manager:registration:confirm:message", array($entity->name, $event->title, event_manager_get_registration_validation_url($event->getGUID(), $entity->getGUID())));

	$site = elgg_get_site_entity();

	// send confirmation mail
	if (elgg_instanceof($entity, "user")) {
		notify_user($entity->getGUID(), $event->getOwnerGUID(), $subject, $message, null, "email");
	} else {

		$from = $site->email;
		if (empty($from)) {
			$from = "noreply@" . $site->getDomain();
		}

		if (!empty($site->name)) {
			$site_name = $site->name;
			if (strstr($site_name, ',')) {
				$site_name = '"' . $site_name . '"'; // Protect the name with quotations if it contains a comma
			}

			$site_name = '=?UTF-8?B?' . base64_encode($site_name) . '?='; // Encode the name. If may content nos ASCII chars.
			$from = $site_name . " <" . $from . ">";
		}

		elgg_send_email($from, $entity->email, $subject, $message);
	}
}

/**
 * Checks if it is allowed to create events in groups
 *
 * @return bool
 */
function event_manager_groups_enabled() {
	static $result;

	if (!isset($result)) {
		$result = true;

		if (!elgg_get_plugin_setting("who_create_group_events", "event_manager")) {
			$result = false;
		}
	}

	return $result;
}
