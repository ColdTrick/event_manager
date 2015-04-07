<?php

	$event_guid = (int) get_input("event_guid");
	$object_guid = (int) get_input("object_guid");
	$forward = true;
	
	if (!empty($event_guid) && ($event = get_entity($event_guid)) && !empty($object_guid) && ($object = get_entity($object_guid))) {
		if (elgg_instanceof($event, "object", Event::SUBTYPE) && (elgg_instanceof($object, "user") || elgg_instanceof($object, "object", EventRegistration::SUBTYPE))) {
			$forward = false;
			
			// set page owner
			elgg_set_page_owner_guid($event->getContainerGUID());
			
			// set breadcrumb
			elgg_push_breadcrumb($event->title, $event->getURL());
			elgg_push_breadcrumb(elgg_echo("event_manager:menu:registration:completed"));
			
			// build page elements
			$title_text = elgg_echo("event_manager:registration:completed:title", array($event->title));
			
			$body = elgg_view("event_manager/registration/completed", array("event" => $event, "object" => $object));
			
			// build page
			$page_data = elgg_view_layout("content", array(
				"title" => $title_text,
				"content" => $body,
				"filter" => ""
			));
			
			// draw page
			echo elgg_view_page($title_text, $page_data);
			
		} elseif(!elgg_instanceof($event, "object", Event::SUBTYPE)) {
			register_error(elgg_echo("ClassException:ClassnameNotClass", array($event_guid, elgg_echo("item:object:" . Event::SUBTYPE))));
		} elseif(!elgg_instanceof($object, "object", EventRegistration::SUBTYPE)) {
			register_error(elgg_echo("ClassException:ClassnameNotClass", array($object_guid, elgg_echo("item:object:" . EventRegistration::SUBTYPE))));
		} else {
			register_error(elgg_echo("ClassException:ClassnameNotClass", array($object_guid, elgg_echo("item:user"))));
		}
	} else {
		register_error(elgg_echo("InvalidParameterException:NoEntityFound"));
	}
	
	if($forward) {
		forward("/events");
	}