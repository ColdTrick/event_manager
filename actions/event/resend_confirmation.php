<?php

	$guid = (int) get_input("guid");
	$user = (int) get_input("user"); // could also be a registration object
	
	if (!empty($guid) && !empty($user)) {
		$object = get_entity($user);
		$event = get_entity($guid);

		if (elgg_instanceof($object) && elgg_instanceof($event, "object", "event")) {
			event_manager_send_registration_validation_email($event, $object);
			system_message(elgg_echo("event_manager:action:resend_confirmation:success"));
		} else {
			register_error(elgg_echo("InvalidParameterException:NoEntityFound"));
		}
	} else {
		register_error(elgg_echo("InvalidParameterException:MissingParameter"));
	}
	
	forward(REFERER);
