<?php

elgg_load_js("event_manager.maps.base");
elgg_require_js("event_manager/googlemaps");

$guid = get_input("guid");

if (!empty($guid) && ($entity = get_entity($guid))) {
	if ($entity instanceof Event) {
		$event = $entity;
		if ($event) {
			if (!$event->waiting_list_enabled) {
				forward($event->getURL());
			}

			if (!$event->openForRegistration()) {
				register_error(elgg_echo('event_manager:event:rsvp:registration_ended'));
				forward($event->getURL());
			}

			$title_text = elgg_echo('event_manager:event:rsvp:waiting_list');

			elgg_push_breadcrumb($event->title, $event->getURL());
			elgg_push_breadcrumb($title_text);

			$form_vars = array("id" => "event_manager_event_register", "name" => "event_manager_event_register");
			$body_vars = array("entity" => $event, "register_type" => "waitinglist");

			$form = elgg_view_form("event_manager/event/register", $form_vars, $body_vars);

			$body = elgg_view_layout('one_sidebar', array(
				'filter' => '',
				'content' => $form,
				'title' => $title_text,
			));

			echo elgg_view_page($title_text, $body);
		}
	}
} else {
	register_error(elgg_echo("InvalidParameterException:GUIDNotFound", array($guid)));
	forward(REFERER);
}
