<?php

	$event_guid = (int) get_input("event_guid");
	$user_guid = (int) get_input("user_guid");
	$code = get_input("code");
	
	// do we have all the correct inputs
	if (empty($event_guid) || empty($user_guid) || empty($code)) {
		register_error(elgg_echo("InvalidParameterException:MissingParameter"));
		forward(REFERER);
	}
	
	// is the code valid
	if (!event_manager_validate_registration_validation_code($event_guid, $user_guid, $code)) {
		register_error(elgg_echo("event_manager:registration:confirm:error:code"));
		forward(REFERER);
	}
	
	$event = get_entity($event_guid);
	$user = get_entity($user_guid);