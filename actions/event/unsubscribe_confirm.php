<?php

	$event_guid = (int) get_input("event");
	$registration_guid = (int) get_input("registration");
	$code = get_input("code");
	
	$forward_url = REFERER;
	
	if (!empty($registration_guid) && !empty($event_guid) && !empty($code)) {
		if (($registration = get_entity($registration_guid)) && elgg_instanceof($registration, "object", EventRegistration::SUBTYPE)) {
			if (($event = get_entity($event_guid)) && elgg_instanceof($event, "object", Event::SUBTYPE)) {
				$verify_code = event_manager_create_unsubscribe_code($registration, $event);
				
				if ($code === $verify_code) {
					if ($event->rsvp(EVENT_MANAGER_RELATION_UNDO, $registration->getGUID())) {
						$forward_url = $event->getURL();
						
						system_message(elgg_echo("event_manager:action:unsubscribe_confirm:success"));
					} else {
						register_error(elgg_echo("event_manager:action:unsubscribe_confirm:error"));
					}
				} else {
					register_error(elgg_echo("event_manager:unsubscribe_confirm:error:code"));
				}
			} else {
				register_error(elgg_echo("ClassException:ClassnameNotClass", array($event_guid, elgg_echo("item:object:" . Event::SUBTYPE))));
			}
		} else {
			register_error(elgg_echo("ClassException:ClassnameNotClass", array($registration_guid, elgg_echo("item:object:" . EventRegistration::SUBTYPE))));
		}
	} else {
		register_error(elgg_echo("InvalidParameterException:MissingParameter"));
	}
	
	forward($forward_url);
	