<?php

$guid = (int) get_input("guid");
$user_guid = (int) get_input("user", elgg_get_logged_in_user_guid());
$rel = get_input("type");

$forward_url = get_input("forward_url", REFERER);
$notice = true;
$rsvp = null;

if (empty($guid) || empty($rel)) {
	register_error(elgg_echo("IOException:FailedToLoadGUID", array("Event", $guid)));
	forward(REFERER);
}

$event = get_entity($guid);
$user = get_entity($user_guid);

if (!$event instanceof Event) {
	register_error(elgg_echo("InvalidClassException:NotValidElggStar", array($guid, "Event")));
	forward(REFERER);
}

if ($rel == EVENT_MANAGER_RELATION_ATTENDING) {
	if ($event->hasEventSpotsLeft() && $event->hasSlotSpotsLeft()) {
		if ($event->registration_needed && $event->hasRegistrationForm()) {
			$forward_url = '/events/event/register/' . $guid . '/' . $rel;
			$notice = false;
		} else {
			$rsvp = $event->rsvp($rel, $user_guid);
		}
	} else {
		if ($event->waiting_list_enabled) {
			$rel = EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST;
			if ($event->openForRegistration()) {
				if ($event->registration_needed && $event->hasRegistrationForm()) {
					$forward_url = '/events/event/waitinglist/' . $guid;
					$notice = false;
				} else {
					$rsvp = $event->rsvp($rel, $user_guid);
				}
			} else {
				register_error(elgg_echo('event_manager:event:rsvp:registration_ended'));
			}
		} else {
			register_error(elgg_echo('event_manager:event:rsvp:nospotsleft'));
		}
	}
} else {
	if ($event->$rel || ($rel == EVENT_MANAGER_RELATION_UNDO && ($event->canEdit() || $user->canEdit()))) {
		$rsvp = $event->rsvp($rel, $user_guid);
	} else {
		register_error(elgg_echo('event_manager:event:relationship:message:unavailable_relation'));
	}
}

if ($notice) {
	if ($rsvp) {
		system_message(elgg_echo('event_manager:event:relationship:message:' . $rel));
	} else {
		register_error(elgg_echo('event_manager:event:relationship:message:error'));
	}
}

forward($forward_url);
