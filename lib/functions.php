<?php
/**
 * Functions for Event Manager
 */

define('EVENT_MANAGER_RELATION_ATTENDING', 'event_attending');
define('EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST', 'event_waitinglist');
define('EVENT_MANAGER_RELATION_ATTENDING_PENDING', 'event_pending');
define('EVENT_MANAGER_RELATION_EXHIBITING', 'event_exhibiting');
define('EVENT_MANAGER_RELATION_ORGANIZING', 'event_organizing');
define('EVENT_MANAGER_RELATION_PRESENTING', 'event_presenting');
define('EVENT_MANAGER_RELATION_INTERESTED', 'event_interested');
define('EVENT_MANAGER_RELATION_UNDO', 'event_undo');

define('EVENT_MANAGER_RELATION_SLOT_REGISTRATION', 'event_slot_registration');
define('EVENT_MANAGER_RELATION_SLOT_REGISTRATION_WAITINGLIST', 'event_slot_registration_waitinglist');
define('EVENT_MANAGER_RELATION_SLOT_REGISTRATION_PENDING', 'event_slot_registration_pending');

/**
 * Returns all relationship options
 *
 * @return array
 */
function event_manager_event_get_relationship_options(): array {
		
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
 * Creates an unsubscribe code
 *
 * @param EventRegistration $registration registration object
 * @param Event             $event        event
 *
 * @return false|string
 */
function event_manager_create_unsubscribe_code(\EventRegistration $registration, \Event $event = null) {
	if (empty($registration) || !$registration instanceof EventRegistration) {
		return false;
	}
	
	if (empty($event) || !$event instanceof \Event) {
		$event = $registration->getOwnerEntity();
	}
	
	return elgg_build_hmac([$registration->guid, $event->time_created])->getToken();
}

/**
 * Returns registration validation url
 *
 * @param int $event_guid guid of event
 * @param int $user_guid  guid of user
 *
 * @return false|string
 */
function event_manager_get_registration_validation_url(int $event_guid, int $user_guid) {
	if (empty($event_guid) || empty($user_guid)) {
		return false;
	}
	
	$code = event_manager_generate_registration_validation_code($event_guid, $user_guid);

	if (empty($code)) {
		return false;
	}
	
	return elgg_generate_url('default:object:eventregistration:confirm', [
		'guid' => $event_guid,
		'user_guid' => $user_guid,
		'code' => $code,
	]);
}

/**
 * Returns registration validation code
 *
 * @param int $event_guid guid of event
 * @param int $user_guid  guid of user
 *
 * @return false|string
 */
function event_manager_generate_registration_validation_code(int $event_guid, int $user_guid) {
	if (empty($event_guid) || empty($user_guid)) {
		return false;
	}
	
	$event = get_entity($event_guid);
	$user = get_entity($user_guid);

	$result = false;
	if (!empty($event) && ($event instanceof \Event) && !empty($user) && (($user instanceof \ElggUser) || ($user instanceof \EventRegistration))) {
		$result = elgg_build_hmac([$event_guid, $user_guid, $event->time_created])->getToken();
	}

	return $result;
}

/**
 * Validates registration validation code
 *
 * @param int $event_guid guid of event
 * @param int $user_guid  guid of user
 * @param string $code       code to validate
 *
 * @return bool
 */
function event_manager_validate_registration_validation_code(int $event_guid, int $user_guid, string $code): bool {
	if (empty($event_guid) || empty($user_guid) || empty($code)) {
		return false;
	}
	
	$valid_code = event_manager_generate_registration_validation_code($event_guid, $user_guid);

	if (empty($valid_code)) {
		return false;
	}
	
	return $code === $valid_code;
}

/**
 * Send registration validation email
 *
 * @param Event      $event  event
 * @param ElggEntity $entity object or user to send mail to
 *
 * @return void
 */
function event_manager_send_registration_validation_email(\Event $event, \ElggEntity $entity): void {
	$subject = elgg_echo('event_manager:registration:confirm:subject', [$event->getDisplayName()]);
	$message = elgg_echo('event_manager:registration:confirm:message', [
			$entity->getDisplayName(),
			$event->getDisplayName(),
			event_manager_get_registration_validation_url($event->guid, $entity->guid)
	]);

	$site = elgg_get_site_entity();

	// send confirmation mail
	if ($entity instanceof \ElggUser) {
		notify_user($entity->guid, $event->getOwnerGUID(), $subject, $message, null, 'email');
	} else {
		elgg_send_email(\Elgg\Email::factory([
			'to' => $entity,
			'subject' => $subject,
			'body' => $message,
		]));
	}
}

/**
 * Returns a formatted date
 *
 * @param int $timestamp
 *
 * @return string
 */
function event_manager_format_date($timestamp): string {
	return gmdate(elgg_echo('event_manager:date:format'), $timestamp);
}

/**
 * Prepares the vars for the event edit form
 *
 * @param \Event $event the event to prepare the vars for
 *
 * @return array
 */
function event_manager_prepare_form_vars(\Event $event = null): array {
	
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
		'event_start' => \Elgg\Values::normalizeTime('+1 hour'), // 1 hour from now
		'event_end' => \Elgg\Values::normalizeTime('+2 hour'), // 2 hours from now
		'registration_ended' => ELGG_ENTITIES_ANY_VALUE,
		'endregistration_day' => ELGG_ENTITIES_ANY_VALUE,
		'with_program' => ELGG_ENTITIES_ANY_VALUE,
		'registration_needed' => ELGG_ENTITIES_ANY_VALUE,
		'register_nologin' => ELGG_ENTITIES_ANY_VALUE,
		'show_attendees' => 1,
		'notify_onsignup' => ELGG_ENTITIES_ANY_VALUE,
		'notify_onsignup_contact' => ELGG_ENTITIES_ANY_VALUE,
		'notify_onsignup_organizer' => ELGG_ENTITIES_ANY_VALUE,
		'max_attendees' => ELGG_ENTITIES_ANY_VALUE,
		'waiting_list_enabled' => ELGG_ENTITIES_ANY_VALUE,
		'access_id' => get_default_access(),
		'container_guid' => elgg_get_page_owner_entity()->guid,
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
		
		$values['event_start'] = \Elgg\Values::normalizeTimestamp($event->getStartDate('d-m-Y H:i:s'));
		$values['event_end'] = \Elgg\Values::normalizeTimestamp($event->getEndDate('d-m-Y H:i:s'));
	}
	
	if (elgg_is_sticky_form('event')) {
		// merge defaults with sticky data
		$values = array_merge($values, elgg_get_sticky_values('event'));
		
		if (isset($values['event_end'])) {
			$event_end = \Elgg\Values::normalizeTime(gmdate('d-m-Y H:i:s', $values['event_end']));
			$event_end->setTime(0,0,0);
			
			$end_time = (int) elgg_extract('end_time', $values);
			$values['event_end'] = $event_end->getTimestamp() + $end_time;
		}
		
		if (isset($values['event_start'])) {
			$event_start = \Elgg\Values::normalizeTime(gmdate('d-m-Y H:i:s', $values['event_start']));
			$event_start->setTime(0,0,0);
			
			$start_time = (int) elgg_extract('start_time', $values);
			$values['event_start'] = $event_start->getTimestamp() + $start_time;
		}
	}
	
	elgg_clear_sticky_form('event');
	
	return $values;
}
