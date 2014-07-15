<?php

function event_manager_event_get_relationship_options()	{
	$result = array(
		EVENT_MANAGER_RELATION_ATTENDING,
		EVENT_MANAGER_RELATION_INTERESTED,
		EVENT_MANAGER_RELATION_PRESENTING,
		EVENT_MANAGER_RELATION_EXHIBITING,
		EVENT_MANAGER_RELATION_ORGANIZING,
		EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST,
		EVENT_MANAGER_RELATION_ATTENDING_PENDING
	);
		
	return $result;
}

function event_manager_search_events($options = array()){
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
		'order_by_metadata' => array("name" => 'start_day', "direction" => 'ASC', "as" => "integer")
	);
	
	if ($options["container_guid"]) {
		// limit for a group
		$entities_options['container_guid'] = $options['container_guid'];
	}
	
	if ($options['query']) {
		$entities_options["joins"][] = "JOIN " . elgg_get_config("dbprefix") . "objects_entity oe ON e.guid = oe.guid";
		$entities_options['wheres'][] = event_manager_search_get_where_sql('oe', array('title', 'description'), $options, false);
	}
				
	if (!empty($options['start_day'])) {
		$entities_options['metadata_name_value_pairs'][] = array('name' => 'start_day', 'value' => $options['start_day'], 'operand' => '>=');
	}
	
	if (!empty($options['end_day'])) {
		$entities_options['metadata_name_value_pairs'][] = array('name' => 'start_day', 'value' => $options['end_day'], 'operand' => '<=');
	}
	
	if (!$options['past_events']) {
		// only show from current day or newer
		$entities_options['metadata_name_value_pairs'][] = array('name' => 'start_day', 'value' => mktime(0, 0, 1), 'operand' => '>=');
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
		
		if ($friends =$user->getFriends("", false)) {
			foreach ($friends as $friend) {
				$friends_guids[] = $friend->getGUID();
			}
			$entities_options['joins'][] = "JOIN " . elgg_get_config("dbprefix") . "entity_relationships e_ra ON e.guid = e_ra.guid_one";
			$entities_options['wheres'][] = "(e_ra.guid_two IN (" . implode(", ", $friends_guids) . "))";
		} else	{
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

function event_manager_get_eventregistrationform_fields($event_guid, $count = false) {
	$entities_options = array(
		'type' => 'object',
		'subtype' => 'eventregistrationquestion',
		'joins' => array(
						"JOIN " . elgg_get_config("dbprefix") . "metadata n_table_r on e.guid = n_table_r.entity_guid",
						"JOIN " . elgg_get_config("dbprefix") . "entity_relationships r on r.guid_one = e.guid"),
		'wheres' => array("r.guid_two = " . $event_guid, "r.relationship = 'event_registrationquestion_relation'"),
		'order_by_metadata' => array("name" => 'order', 'direction' => 'ASC', "as" => "integer"),
		'count' => $count,
		'limit' => false
	);
	
	if($entities = elgg_get_entities_from_metadata($entities_options)) {
		return $entities;
	} else {
		return false;
	}
}

function get_entities_from_viewport($lat, $long, $radius, $type = "", $subtype = "", $limit = 20) {
	if (empty($subtype)) {
		return false;
	}
	
	$lat = (real) $lat;
	$long = (real) $long;
	$radius = (real) $radius;
	
	$limit = (int) $limit;
	$offset = 0;
	
	$site_guid = elgg_get_site_entity()->getGUID();
	$dbprefix = elgg_get_config("dbprefix");
	
	$where = array();
	 
	if (is_array($type)) {
		$tempwhere = "";
		if (sizeof($type)) {
			foreach ($type as $typekey => $subtypearray) {
				foreach ($subtypearray as $subtypeval) {
					$typekey = sanitise_string($typekey);
					if (!empty($subtypeval)) {
						$subtypeval = (int) get_subtype_id($typekey, $subtypeval);
					} else {
						$subtypeval = 0;
					}
					if (!empty($tempwhere)) {
						$tempwhere .= " or ";
					}
					$tempwhere .= "(e.type = '{$typekey}' AND e.subtype = {$subtypeval})";
				}
			}
		}
		if (!empty($tempwhere)) {
			$where[] = "({$tempwhere})";
		}
	} else {
		$type = sanitise_string($type);
		$subtype = get_subtype_id($type, $subtype);
		
		if ($type != "") {
			$where[] = "e.type='$type'";
		}
		
		if ($subtype!=="") {
			$where[] = "e.subtype=$subtype";
		}
	}
	
	if ($owner_guid != "") {
		if (!is_array($owner_guid)) {
			$owner_array = array($owner_guid);
			$owner_guid = (int) $owner_guid;
			$where[] = "e.owner_guid = '$owner_guid'";
		} else if (sizeof($owner_guid) > 0) {
			$owner_array = array_map('sanitise_int', $owner_guid);
			
			// Cast every element to the owner_guid array to int
			$owner_guid = implode(",",$owner_guid); //
			$where[] = "e.owner_guid in ({$owner_guid})" ; //
		}
		if (is_null($container_guid)) {
			$container_guid = $owner_array;
		}
	}
	
	if ($site_guid > 0) {
		$where[] = "e.site_guid = {$site_guid}";
	}
	
	if (!is_null($container_guid)) {
		if (is_array($container_guid)) {
			foreach ($container_guid as $key => $val) {
				$container_guid[$key] = (int) $val;
			}
			$where[] = "e.container_guid in (" . implode(",",$container_guid) . ")";
		} else {
			$container_guid = (int) $container_guid;
			$where[] = "e.container_guid = {$container_guid}";
		}
	}
	
	// Add the calendar stuff
	$loc_join = "JOIN " . $dbprefix . "metadata loc_start on e.guid=loc_start.entity_guid";
	$loc_join .= "JOIN " . $dbprefix . "metastrings loc_start_name on loc_start.name_id=loc_start_name.id";
	$loc_join .= "JOIN " . $dbprefix . "metastrings loc_start_value on loc_start.value_id=loc_start_value.id";
	$loc_join .= "JOIN " . $dbprefix . "metadata loc_end on e.guid=loc_end.entity_guid";
	$loc_join .= "JOIN " . $dbprefix . "metastrings loc_end_name on loc_end.name_id=loc_end_name.id";
	$loc_join .= "JOIN " . $dbprefix . "metastrings loc_end_value on loc_end.value_id=loc_end_value.id";
	
	$lat_min = $lat - $radius;
	$lat_max = $lat + $radius;
	$long_min = $long - $radius;
	$long_max = $long + $radius;
	
	$where[] = "loc_start_name.string = 'geo:lat'";
	$where[] = "loc_start_value.string >= $lat_min";
	$where[] = "loc_start_value.string <= $lat_max";
	$where[] = "loc_end_name.string = 'geo:long'";
	$where[] = "loc_end_value.string >= $long_min";
	$where[] = "loc_end_value.string <= $long_max";
	
	$query = "SELECT e.* from " . $dbprefix . "entities e $loc_join where ";
	
	foreach ($where as $w) {
		$query .= " $w and ";
	}
	
	$query .= get_access_sql_suffix('e'); // Add access controls
	
	// Add order and limit
	if ($limit) {
		$query .= " limit $offset, $limit";
	}
	$dt = get_data($query, "entity_row_to_elggstar");
	
	return $dt;
}

function event_manager_export_attendees($event, $file = false) {
	$old_ia = elgg_set_ignore_access(true);
	
	if ($file) {
		$EOL = "\r\n";
	} else {
		$EOL = PHP_EOL;
	}
	
	$headerString .= '"'.elgg_echo('guid').'";"'.elgg_echo('name').'";"'.elgg_echo('email').'";"'.elgg_echo('username').'"';
	
	if ($event->registration_needed) {
		if ($registration_form = $event->getRegistrationFormQuestions()) {
			foreach ($registration_form as $question) {
				$headerString .= ';"'.$question->title.'"';
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
	
	if ($attendees = $event->exportAttendees()) {
		foreach ($attendees as $attendee) {
			$answerString = '';
			
			$dataString .= '"'.$attendee->guid.'";"'.$attendee->name.'";"'.$attendee->email.'";"'.$attendee->username.'"';
		
			if ($event->registration_needed) {
				if ($registration_form = $event->getRegistrationFormQuestions()) {
					foreach ($registration_form as $question) {
						$answer = $question->getAnswerFromUser($attendee->getGUID());
						
						$answerString .= '"'.addslashes($answer->value).'";';
					}
				}
				$dataString .= ';'.substr($answerString, 0, (strlen($answerString) -1));
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
			
			$dataString .= $EOL;
		}
	}
	
	$headerString .= $EOL;
	elgg_set_ignore_access($old_ia);
	
	return $headerString . $dataString;
}

function event_manager_export_waitinglist($event, $file = false) {
	$old_ia = elgg_set_ignore_access(true);
	if ($file) {
		$EOL = "\r\n";
	} else {
		$EOL = PHP_EOL;
	}
	
	$headerString .= '"'.elgg_echo('guid').'";"'.elgg_echo('name').'";"'.elgg_echo('email').'";"'.elgg_echo('username').'"';
	
	if ($event->registration_needed) {
		if ($registration_form = $event->getRegistrationFormQuestions()) {
			foreach ($registration_form as $question) {
				$headerString .= ';"'.$question->title.'"';
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
						
						$headerString .= ';"Event activity: \''  .addslashes($eventSlot->title) . '\' ' . $date . ' (' . $start_time_hour . ':' . $start_time_minutes . ' - ' . $end_time_hour . ':' . $end_time_minutes . ')"';
					}
				}
			}
		}
	}
	
	if ($waiters = $event->exportWaiters()) {
		foreach ($waiters as $waiter) {
			$answerString = '';
			
			$dataString .= '"'.$waiter->guid.'";"'.$waiter->name.'";"'.$waiter->email.'";"'.$waiter->username.'"';
		
			if ($event->registration_needed) {
				if ($registration_form = $event->getRegistrationFormQuestions()) {
					foreach ($registration_form as $question) {
						$answer = $question->getAnswerFromUser($waiter->getGUID());
						
						$answerString .= '"'.addslashes($answer->value).'";';
					}
				}
				$dataString .= ';'.substr($answerString, 0, (strlen($answerString) -1));
			}
			
			if ($event->with_program) {
				if ($eventDays = $event->getEventDays()) {
					foreach ($eventDays as $eventDay) {
						if ($eventSlots = $eventDay->getEventSlots()) {
							foreach ($eventSlots as $eventSlot) {
								if (check_entity_relationship($waiter->getGUID(), EVENT_MANAGER_RELATION_SLOT_REGISTRATION, $eventSlot->getGUID())) {
									$dataString .= ';"V"';
								} else {
									$dataString .= ';""';
								}
							}
						}
					}
				}
			}
			
			$dataString .= $EOL;
		}
	}
	
	$headerString .= $EOL;
	elgg_set_ignore_access($old_ia);
	
	return $headerString . $dataString;
}

function event_manager_sanitize_filename($string, $force_lowercase = true, $anal = false) {
    $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
                   "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
                   "—", "–", ",", "<", ">", "/", "?");
    $clean = trim(str_replace($strip, "", strip_tags($string)));
    $clean = preg_replace('/\s+/', "-", $clean);
    $clean = ($anal) ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean ;
    return ($force_lowercase) ?
        (function_exists('mb_strtolower')) ?
            mb_strtolower($clean, 'UTF-8') :
            strtolower($clean) :
        $clean;
}

function event_manager_search_get_where_sql($table, $fields, $params, $use_fulltext = true)	{
	
	// TODO: why not use a search hook?
	$query = $params['query'];
	
	// add the table prefix to the fields
	foreach ($fields as $i => $field) {
		if ($table) {
			$fields[$i] = "$table.$field";
		}
	}
	
	$where = '';
	
	$likes = array();
	$query = sanitise_string($query);
	foreach ($fields as $field) {
		$likes[] = "$field LIKE '%$query%'";
	}
	$likes_str = implode(' OR ', $likes);
	$where = "($likes_str)";
	
	return $where;
}

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

function event_manager_event_type_options()	{
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

function event_manager_get_form_pulldown_hours($name = '', $value = '', $h = 23) {
	$time_hours_options = range(0, $h);
	
	array_walk($time_hours_options, 'event_manager_time_pad');
	
	return elgg_view('input/dropdown', array('name' => $name, 'value' => $value, 'options' => $time_hours_options));
}

function event_manager_get_form_pulldown_minutes($name = '', $value = '') {
	$time_minutes_options = range(0, 59, 5);
	
	array_walk($time_minutes_options, 'event_manager_time_pad');
	
	return elgg_view('input/dropdown', array('name' => $name, 'value' => $value, 'options' => $time_minutes_options));
}

function event_manager_time_pad(&$value) {
    $value = str_pad($value, 2, "0", STR_PAD_LEFT);
}

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

function event_manager_send_registration_validation_email($event, $object) {
	$subject = elgg_echo("event_manager:registration:confirm:subject", array($event->title));
	$message = elgg_echo("event_manager:registration:confirm:message", array($object->name, $event->title, event_manager_get_registration_validation_url($event->getGUID(), $object->getGUID())));
	
	$site = elgg_get_site_entity();
	
	// send confirmation mail
	if (elgg_instanceof($object, "user")) {
		notify_user($object->getGUID(), $site->getGUID(), $subject, $message, null, "email");
	} else {
			
		$from = $site->email;
		if (empty($from)) {
			$from = "noreply@" . get_site_domain($site->getGUID());
		}
			
		if (!empty($site->name)) {
			$site_name = $site->name;
			if (strstr($site_name, ',')) {
				$site_name = '"' . $site_name . '"'; // Protect the name with quotations if it contains a comma
			}
	
			$site_name = '=?UTF-8?B?' . base64_encode($site_name) . '?='; // Encode the name. If may content nos ASCII chars.
			$from = $site_name . " <" . $from . ">";
		}
			
		elgg_send_email($from, $object->email, $subject, $message);
	}
}

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