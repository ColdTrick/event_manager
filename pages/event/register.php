<?php

elgg_load_js("event_manager.maps.base");
elgg_require_js("event_manager/googlemaps");

$guid = get_input("guid");
$relation = get_input("relation");

if (!empty($guid) && ($entity = get_entity($guid))) {
	if ($entity->getSubtype() == Event::SUBTYPE) {
		$event = $entity;

		if ((!$event->registration_needed && elgg_is_logged_in()) || (!elgg_is_logged_in() && !$event->register_nologin)) {
			system_message(elgg_echo('event_manager:registration:message:registrationnotneeded'));
			forward($event->getURL());
		}

		if (!elgg_is_logged_in()) {
			if (!$event->hasEventSpotsLeft() || !$event->hasSlotSpotsLeft()) {
				if ($event->waiting_list_enabled && $event->registration_needed && $event->openForRegistration()) {
					forward('/events/event/waitinglist/' . $guid);
				} else {
					register_error(elgg_echo('event_manager:event:rsvp:nospotsleft'));
					forward(REFERER);
				}
			}
		}

		if (!$event->openForRegistration()) {
			register_error(elgg_echo("event_manager:event:rsvp:registration_ended"));
			forward($event->getURL());
		}

		$form_vars = array("id" => "event_manager_event_register", "name" => "event_manager_event_register");
		$body_vars = array("entity" => $event);

		$form = elgg_view_form("event_manager/event/register", $form_vars, $body_vars);

		$title_text = elgg_echo("event_manager:registration:register:title");

		elgg_set_page_owner_guid($event->getContainerGUID());

		elgg_push_breadcrumb($event->title, $event->getURL());
		elgg_push_breadcrumb($title_text);

		$title = $title_text . " '" . $event->title . "'";

		$body = elgg_view_layout('content', array(
			'filter' => '',
			'content' => $form,
			'title' => $title,
		));
		
		$page_vars = [];
		if ($event->hide_owner_block) {
			$page_vars['body_attrs'] = ['class' => 'event-manager-hide-owner-block'];
		}

		echo elgg_view_page($title, $body, 'default', $page_vars);

		// TODO: replace with sticky form functionality
		$_SESSION['registerevent_values'] = null;
	}
} else {
	register_error(elgg_echo("InvalidParameterException:GUIDNotFound", array($guid)));
	forward(REFERER);
}
