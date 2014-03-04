<?php

	$event_guid = (int) get_input("event_guid");
	$user_guid = (int) get_input("user_guid");
	$code = get_input("code");
	
	// do we have all the correct inputs
	if (empty($event_guid) || empty($user_guid) || empty($code)) {
		register_error(elgg_echo("InvalidParameterException:MissingParameter"));
		forward();
	}
	
	// is the code valid
	if (!event_manager_validate_registration_validation_code($event_guid, $user_guid, $code)) {
		register_error(elgg_echo("event_manager:registration:confirm:error:code"));
		forward();
	}
	
	$event = get_entity($event_guid);
	$user = get_entity($user_guid);
	
	// do we have a pending registration
	if ($event->getRelationshipByUser($user_guid) != EVENT_MANAGER_RELATION_ATTENDING_PENDING) {
		forward($event->getURL());
	}
	
	// set page owner
	elgg_set_page_owner_guid($event->getContainerGUID());
	
	// build breadcrumb
	elgg_push_breadcrumb($event->title, $event->getURL());
	elgg_push_breadcrumb(elgg_echo("event_manager:registration:confirm:breadbrumb"));
	
	// let's show the confirm form
	$title_text = elgg_echo("event_manager:registration:confirm:title", array($event->title));
	
	$form_vars = array();
	$body_vars = array(
		"event" => $event,
		"user" => $user,
		"code" => $code
	);
	$form = elgg_view_form("event_manager/registration/confirm", $form_vars, $body_vars);
	
	// build page
	$page_data = elgg_view_layout("content", array(
		"title" => $title_text,
		"content" => $form,
		"filter" => ""
	));
	
	// draw page
	echo elgg_view_page($title_text, $page_data);