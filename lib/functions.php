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
	
	static $result;
	if (isset($result)) {
		return $result;
	}
	
	$result = [
		EVENT_MANAGER_RELATION_ATTENDING,
		EVENT_MANAGER_RELATION_PRESENTING,
		EVENT_MANAGER_RELATION_EXHIBITING,
		EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST,
		EVENT_MANAGER_RELATION_ATTENDING_PENDING,
	];
	
	if (elgg_get_plugin_setting('rsvp_interested', 'event_manager') !== 'no') {
		$result[] = EVENT_MANAGER_RELATION_INTERESTED;
	}
	
	return $result;
}

/**
 * Search for events
 *
 * @param array $options search options
 *
 * @return array
 */
function event_manager_search_events($options = []) {
	$dbprefix = elgg_get_config('dbprefix');
	
	$defaults = [
		'past_events' => false,
		'count' => false,
		'limit' => (int) get_input('limit', 10),
		'offset' => (int) get_input('offset', 0),
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
		'event_start' => null,
		'event_end' => null,
		'search_type' => "list",
		'user_guid' => elgg_get_logged_in_user_guid(),
	];

	$options = array_merge($defaults, $options);

	$entities_options = [
		'type' => 'object',
		'subtype' => 'event',
		'offset' => $options['offset'],
		'limit' => $options['limit'],
		'joins' => [],
		'wheres' => [],
		'order_by_metadata' => [
			'name' => 'event_start',
			'direction' => 'ASC',
			'as' => 'integer'
		]
	];

	if ($options['container_guid']) {
		// limit for a group
		$entities_options['container_guid'] = $options['container_guid'];
	}

	if ($options['query']) {
		$entities_options['joins'][] = "JOIN {$dbprefix}objects_entity oe ON e.guid = oe.guid";
		$entities_options['wheres'][] = event_manager_search_get_where_sql('oe', ['title', 'description'], $options);
	}

	if (!empty($options['event_start'])) {
		$entities_options['metadata_name_value_pairs'][] = [
			'name' => 'event_start',
			'value' => $options['event_start'],
			'operand' => '>='
		];
	}

	if (!empty($options['event_end'])) {
		$options['event_end'] += 86400; // add one day
		$entities_options['metadata_name_value_pairs'][] = [
			'name' => 'event_end',
			'value' => $options['event_end'],
			'operand' => '<='
		];
	}

	if (!$options['past_events']) {
		// only show from current day or newer (or where event is still running)
		$current_time = gmmktime(0, 0, 1);
		if ($options['event_end']) {
			$entities_options['metadata_name_value_pairs'][] = [
				'name' => 'event_start',
				'value' => $current_time,
				'operand' => '>='
			];
		} else {
			// start date
			$event_start_id = elgg_get_metastring_id('event_start');
			$entities_options['joins'][] = "JOIN {$dbprefix}metadata md_start ON e.guid = md_start.entity_guid";
			$entities_options['joins'][] = "JOIN {$dbprefix}metastrings msv_start ON md_start.value_id = msv_start.id";
			$entities_options['wheres'][] = "md_start.name_id = {$event_start_id}";
			
			// end date
			$event_end_id = elgg_get_metastring_id('event_end');
			$entities_options['joins'][] = "JOIN {$dbprefix}metadata md_end ON e.guid = md_end.entity_guid";
			$entities_options['joins'][] = "JOIN {$dbprefix}metastrings msv_end ON md_end.value_id = msv_end.id";
			$entities_options['wheres'][] = "md_end.name_id = {$event_end_id}";
			
			// event start > now
			$time_start = "(msv_start.string >= {$current_time})";
			
			// or event start before end and end after now
			$time_end = "((msv_start.string < {$current_time}) AND (msv_end.string > {$current_time}))";
			
			$entities_options['wheres'][] = "({$time_start} OR {$time_end})";
		}
	}

	if ($options['meattending'] && !empty($options['user_guid'])) {
		$entities_options['joins'][] = "JOIN {$dbprefix}entity_relationships e_r ON e.guid = e_r.guid_one";

		$entities_options['wheres'][] = 'e_r.guid_two = ' . $options['user_guid'];
		$entities_options['wheres'][] = 'e_r.relationship = "' . EVENT_MANAGER_RELATION_ATTENDING . '"';
	}

	if ($options['owning'] && !empty($options['user_guid'])) {
		$entities_options['owner_guids'] = [$options['user_guid']];
	}

	if ($options['region']) {
		$entities_options['metadata_name_value_pairs'][] = [
			'name' => 'region',
			'value' => $options['region']
		];
	}

	if ($options['event_type']) {
		$entities_options['metadata_name_value_pairs'][] = [
			'name' => 'event_type',
			'value' => $options['event_type']
		];
	}

	if ($options['friendsattending'] && !empty($options['user_guid'])) {
		$friends_guids = [];
		$user = get_entity($options['user_guid']);

		if ($friends = $user->getFriends('', false)) {
			foreach ($friends as $friend) {
				$friends_guids[] = $friend->getGUID();
			}
			$entities_options['joins'][] = "JOIN {$dbprefix}entity_relationships e_ra ON e.guid = e_ra.guid_one";
			$entities_options['wheres'][] = '(e_ra.guid_two IN (' . implode(', ', $friends_guids) . '))';
		} else {
			// return no result
			$entities_options['joins'] = [];
			$entities_options['wheres'] = ['(1=0)'];
		}
	}
	
	if (($options['search_type'] == 'onthemap') && !empty($options['latitude']) && !empty($options['longitude']) && !empty($options['distance'])) {
		$entities_options['latitude'] = $options['latitude'];
		$entities_options['longitude'] = $options['longitude'];
		$entities_options['distance'] = $options['distance'];
		$entities = elgg_get_entities_from_location($entities_options);

		$entities_options['count'] = true;
		$count_entities = elgg_get_entities_from_location($entities_options);

	} else {
		$entities = elgg_get_entities_from_metadata($entities_options);

		$entities_options['count'] = true;
		$count_entities = elgg_get_entities_from_metadata($entities_options);
	}
	
	$result = [
		'entities' => $entities,
		'count' => $count_entities
	];

	return $result;
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
	$region_settings = trim(elgg_get_plugin_setting('region_list', 'event_manager'));

	if (empty($region_settings)) {
		return false;
	}
	
	$region_options = ['-'];
	$region_list = explode(',', $region_settings);
	$region_options = array_merge($region_options, $region_list);

	array_walk($region_options, create_function('&$val', '$val = trim($val);'));

	return $region_options;
}

/**
 * Returns event type options
 *
 * @return bool|array
 */
function event_manager_event_type_options() {
	$type_settings = trim(elgg_get_plugin_setting('type_list', 'event_manager'));

	if (empty($type_settings)) {
		return false;
	}
	$type_options = array('-');
	$type_list = explode(',', $type_settings);
	$type_options = array_merge($type_options, $type_list);

	array_walk($type_options, create_function('&$val', '$val = trim($val);'));

	return $type_options;
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
	if (empty($registration) || !elgg_instanceof($registration, 'object', EventRegistration::SUBTYPE)) {
		return false;
	}
	
	if (empty($event) || !elgg_instanceof($event, 'object', Event::SUBTYPE)) {
		$event = $registration->getOwnerEntity();
	}
	
	return elgg_build_hmac([$registration->getGUID(), $event->time_created])->getToken();
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
	if (empty($event_guid) || empty($user_guid)) {
		return false;
	}
	
	$code = event_manager_generate_registration_validation_code($event_guid, $user_guid);

	if (empty($code)) {
		return false;
	}
	
	$result = 'events/registration/confirm/' . $event_guid . '?user_guid=' . $user_guid . '&code=' . $code;
	return elgg_normalize_url($result);
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
	if (empty($event_guid) || empty($user_guid)) {
		return false;
	}
	
	$event = get_entity($event_guid);
	$user = get_entity($user_guid);

	$result = false;
	if (!empty($event) && elgg_instanceof($event, 'object', Event::SUBTYPE) && !empty($user) && (elgg_instanceof($user, 'user') || elgg_instanceof($user, 'object', EventRegistration::SUBTYPE))) {
		$result = elgg_build_hmac([$event_guid, $user_guid, $event->time_created])->getToken();
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
	if (empty($event_guid) || empty($user_guid) || empty($code)) {
		return false;
	}
	
	$valid_code = event_manager_generate_registration_validation_code($event_guid, $user_guid);

	if (empty($valid_code)) {
		return false;
	}
	
	if ($code !== $valid_code) {
		return false;
	}
	
	return true;
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
	$subject = elgg_echo('event_manager:registration:confirm:subject', [$event->title]);
	$message = elgg_echo('event_manager:registration:confirm:message', [
			$entity->name,
			$event->title,
			event_manager_get_registration_validation_url($event->getGUID(), $entity->getGUID())
	]);

	$site = elgg_get_site_entity();

	// send confirmation mail
	if (elgg_instanceof($entity, 'user')) {
		notify_user($entity->getGUID(), $event->getOwnerGUID(), $subject, $message, null, 'email');
	} else {

		$from = $site->email;
		if (empty($from)) {
			$from = 'noreply@' . $site->getDomain();
		}

		if (!empty($site->name)) {
			$site_name = $site->name;
			if (strstr($site_name, ',')) {
				$site_name = '"' . $site_name . '"'; // Protect the name with quotations if it contains a comma
			}

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

	if (isset($result)) {
		return $result;
	}
	
	$result = true;

	if (!elgg_get_plugin_setting('who_create_group_events', 'event_manager')) {
		$result = false;
	}

	return $result;
}

/**
 * Returns a formatted date
 *
 * @param int $timestamp
 *
 * @return string
 */
function event_manager_format_date($timestamp) {
	return gmdate(elgg_echo('event_manager:date:format'), $timestamp);
}

/**
 * Registers event list title menu item to add an event
 *
 * @return void
 */
function event_manager_register_title_menu() {
	$page_owner = elgg_get_page_owner_entity();
	if ($page_owner instanceof \ElggGroup) {
		if (event_manager_can_create_group_events($page_owner)) {
			elgg_register_menu_item('title', [
				'name' => 'new',
				'href' => 'events/event/new/' . $page_owner->getGUID(),
				'text' => elgg_echo('event_manager:menu:new_event'),
				'link_class' => 'elgg-button elgg-button-action',
			]);
		}
	} elseif (event_manager_can_create_site_events()) {
		elgg_register_menu_item('title', [
			'name' => 'new',
			'href' => 'events/event/new',
			'text' => elgg_echo('event_manager:menu:new_event'),
			'link_class' => 'elgg-button elgg-button-action',
		]);
	}
}

/**
 * Checks if a certain user can create group events
 *
 * @param $group Group to check rights for
 * @param $user  User to check rights for
 *
 * @return bool
 */
function event_manager_can_create_group_events(\ElggGroup $group, $user = null) {
	if (empty($user)) {
		$user = elgg_get_logged_in_user_entity();
	}
	
	if (!($group instanceof \ElggGroup) || !($user instanceof \ElggUser)) {
		return false;
	}
	
	$who_create_group_events = elgg_get_plugin_setting('who_create_group_events', 'event_manager'); // group_admin, members
	switch ($who_create_group_events) {
		case 'group_admin':
			return $group->canEdit($user->guid);
		case 'members':
			if ($group->isMember($user)) {
				return true;
			} else {
				return $group->canEdit($user->guid);
			}
	}
		
	return false;
}

/**
 * Checks if a certain user can create site events
 *
 * @param $user User to check rights for
 *
 * @return bool
 */
function event_manager_can_create_site_events($user = null) {
	if (empty($user)) {
		$user = elgg_get_logged_in_user_entity();
	}
	
	if (!($user instanceof \ElggUser)) {
		return false;
	}
	
	$who_create_site_events = elgg_get_plugin_setting('who_create_site_events', 'event_manager');
	if ($who_create_site_events !== 'admin_only') {
		return true;
	}
	
	return elgg_is_admin_logged_in();
}

/**
 * Prepares the vars for the event edit form
 *
 * @param \Event $event the event to prepare the vars for
 *
 * @return array
 */
function event_manager_prepare_form_vars($event = null) {
	
	// defaults
	$values = [
		'guid' => ELGG_ENTITIES_ANY_VALUE,
		'title' => ELGG_ENTITIES_ANY_VALUE,
		'shortdescription' => ELGG_ENTITIES_ANY_VALUE,
		'tags' => ELGG_ENTITIES_ANY_VALUE,
		'description' => ELGG_ENTITIES_ANY_VALUE,
		'comments_on' => 1,
		'venue' => ELGG_ENTITIES_ANY_VALUE,
		'location' => ELGG_ENTITIES_ANY_VALUE,
		'latitude' => ELGG_ENTITIES_ANY_VALUE,
		'longitude' => ELGG_ENTITIES_ANY_VALUE,
		'region' => ELGG_ENTITIES_ANY_VALUE,
		'event_type' => ELGG_ENTITIES_ANY_VALUE,
		'website' => ELGG_ENTITIES_ANY_VALUE,
		'contact_details' => ELGG_ENTITIES_ANY_VALUE,
		'contact_guids' => ELGG_ENTITIES_ANY_VALUE,
		'fee' => ELGG_ENTITIES_ANY_VALUE,
		'fee_details' => ELGG_ENTITIES_ANY_VALUE,
		'organizer' => ELGG_ENTITIES_ANY_VALUE,
		'organizer_guids' => ELGG_ENTITIES_ANY_VALUE,
		'event_start' => gmmktime(date('H')) + 3600, // 1 hour from now
		'event_end' => gmmktime(date('H')) + 3600 + 3600, // 2 hours from now
		'registration_ended' => ELGG_ENTITIES_ANY_VALUE,
		'endregistration_day' => ELGG_ENTITIES_ANY_VALUE,
		'with_program' => ELGG_ENTITIES_ANY_VALUE,
		'registration_needed' => ELGG_ENTITIES_ANY_VALUE,
		'register_nologin' => ELGG_ENTITIES_ANY_VALUE,
		'show_attendees' => 1,
		'notify_onsignup' => ELGG_ENTITIES_ANY_VALUE,
		'max_attendees' => ELGG_ENTITIES_ANY_VALUE,
		'waiting_list_enabled' => ELGG_ENTITIES_ANY_VALUE,
		'access_id' => get_default_access(),
		'container_guid' => elgg_get_page_owner_entity()->getGUID(),
		'event_interested' => 0,
		'event_presenting' => 0,
		'event_exhibiting' => 0,
		'registration_completed' => ELGG_ENTITIES_ANY_VALUE,
	];

	if ($event instanceof \Event) {
		// edit mode
		$values['latitude'] = $event->getLatitude();
		$values['longitude'] = $event->getLongitude();
		$values['tags'] = string_to_tag_array($event->tags);
	
		foreach ($values as $field => $value) {
			if (!in_array($field, ['latitude', 'longitude', 'tags'])) {
				$values[$field] = $event->$field;
			}
		}
		
		if (!empty($values['endregistration_day'])) {
			$values['endregistration_day'] = date('Y-m-d', $values['endregistration_day']);
		}
	}
	
	if (elgg_is_sticky_form('event')) {
		// merge defaults with sticky data
		$values = array_merge($values, elgg_get_sticky_values('event'));
	}
	
	elgg_clear_sticky_form('event');
	
	return $values;
}
