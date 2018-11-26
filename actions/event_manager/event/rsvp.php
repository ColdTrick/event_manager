<?php

$guid = (int) get_input('guid');
$user_guid = (int) get_input('user', elgg_get_logged_in_user_guid());
$rel = get_input('type');

$forward_url = get_input('forward_url', REFERER);
$rsvp = null;

elgg_entity_gatekeeper($guid, 'object', Event::SUBTYPE);
$event = get_entity($guid);

elgg_entity_gatekeeper($user_guid);
$user = get_entity($user_guid);

if (!elgg_is_logged_in()) {
	$code = get_input('code');
	if (!event_manager_validate_registration_validation_code($guid, $user_guid, $code)) {
		return elgg_error_response(elgg_echo('event_manager:registration:confirm:error:code'));
	}
} elseif (!$user->canEdit() && !$event->canEdit()) {
	return elgg_echo('actionunauthorized');
}

if (empty($rel)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

if ($rel == EVENT_MANAGER_RELATION_ATTENDING) {
	if ($event->hasEventSpotsLeft() && $event->hasSlotSpotsLeft()) {
		if ($event->registration_needed && $event->hasRegistrationForm()) {
			return elgg_redirect_response(elgg_generate_url('default:object:event:register', [
				'guid' => $guid,
				'relation' => $rel,
			]));
		} else {
			$rsvp = $event->rsvp($rel, $user_guid);
		}
	} else {
		if ($event->waiting_list_enabled) {
			$rel = EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST;
			if ($event->openForRegistration()) {
				if ($event->registration_needed && $event->hasRegistrationForm()) {
					return elgg_redirect_response(elgg_generate_url('collection:object:event:waitinglist', ['guid' => $guid]));
				} else {
					$rsvp = $event->rsvp($rel, $user_guid);
				}
			} else {
				return elgg_error_response(elgg_echo('event_manager:event:rsvp:registration_ended'));
			}
		} else {
			return elgg_error_response(elgg_echo('event_manager:event:rsvp:nospotsleft'));
		}
	}
} else {
	if ($event->$rel || ($rel == EVENT_MANAGER_RELATION_UNDO)) {
		$rsvp = $event->rsvp($rel, $user_guid);
	} else {
		return elgg_error_response(elgg_echo('event_manager:event:relationship:message:unavailable_relation'));
	}
}

if (!$rsvp) {
	return elgg_error_response(elgg_echo('event_manager:event:relationship:message:error'));
}

return elgg_ok_response('', elgg_echo("event_manager:event:relationship:message:{$rel}"), $forward_url);
