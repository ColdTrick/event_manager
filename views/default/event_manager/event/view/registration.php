<?php

$event = elgg_extract('entity', $vars);
if (!$event instanceof \Event) {
	return;
}

$registration_details = '';
$max_attendees = $event->max_attendees;
$fee = $event->fee;
$type = $event->event_type;
$endregistration_day = $event->endregistration_day;

if ($endregistration_day) {
	$registration_details .= elgg_view_image_block(
		elgg_format_element('label', [], elgg_echo('event_manager:edit:form:endregistration_day') . ':'),
		elgg_view('output/date', ['value' => $endregistration_day])
	);
}

if ($max_attendees) {
	$attendee_info = '';
	
	$spots_left = ($max_attendees - $event->countAttendees());
	if ($spots_left < 1) {
		$count_waitinglist = $event->countWaiters();
		if ($count_waitinglist > 0) {
			$attendee_info .= elgg_echo('event_manager:full') . ', ' . $count_waitinglist . ' ';
			if ($count_waitinglist == 1) {
				$attendee_info .= elgg_echo('event_manager:personwaitinglist');
			} else {
				$attendee_info .= elgg_echo('event_manager:peoplewaitinglist');
			}
		} else {
			$attendee_info .= elgg_echo('event_manager:full');
		}
	} else {
		$attendee_info .= $spots_left . ' / ' . $max_attendees;
	}
	
	$registration_details .= elgg_view_image_block(
		elgg_format_element('label', [], elgg_echo('event_manager:edit:form:spots_left') . ':'),
		$attendee_info
	);
}

if ($fee) {
	$registration_details .= elgg_view_image_block(
		elgg_format_element('label', [], elgg_echo('event_manager:edit:form:fee') . ':'),
		elgg_view('output/text', ['value' => $fee]) . elgg_view('output/longtext', ['value' => $event->fee_details])
	);
}

if ($type) {
	$registration_details .= elgg_view_image_block(
		elgg_format_element('label', [], elgg_echo('event_manager:edit:form:type') . ':'),
		$type
	);
}

$content = elgg_format_element('div', [], $registration_details);
$content .= elgg_format_element('div', [], elgg_view('event_manager/event/rsvp', $vars));

echo elgg_format_element('div', ['class' => 'event-manager-view-registration-details'], $content);
