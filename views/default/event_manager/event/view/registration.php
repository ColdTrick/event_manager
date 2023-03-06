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
	$registration_details .= '<div class="elgg-columns">';
	$registration_details .= '<label class="elgg-col">' . elgg_echo('event_manager:edit:form:endregistration_day') . ':</label>';
	$registration_details .= '<span class="elgg-col">' . elgg_view('output/date', ['value' => $endregistration_day]) . '</span>';
	$registration_details .= '</div>';
}

if ($max_attendees) {
	$registration_details .= '<div class="elgg-columns">';
	$registration_details .= '<label class="elgg-col">' . elgg_echo('event_manager:edit:form:spots_left') . ':</label>';
	$registration_details .= '<span class="elgg-col">';
	
	$spots_left = ($max_attendees - $event->countAttendees());
	if ($spots_left < 1) {
		$count_waitinglist = $event->countWaiters();
		if ($count_waitinglist > 0) {
			$registration_details .= elgg_echo('event_manager:full') . ', ' . $count_waitinglist . ' ';
			if ($count_waitinglist == 1) {
				$registration_details .= elgg_echo('event_manager:personwaitinglist');
			} else {
				$registration_details .= elgg_echo('event_manager:peoplewaitinglist');
			}
		} else {
			$registration_details .= elgg_echo('event_manager:full');
		}
	} else {
		$registration_details .= $spots_left . ' / ' . $max_attendees;
	}

	$registration_details .= '</span>';
	$registration_details .= '</div>';
}

if ($fee) {
	$registration_details .= '<div class="elgg-columns">';
	$registration_details .= '<label class="elgg-col">' . elgg_echo('event_manager:edit:form:fee') . ':</label>';
	$registration_details .= '<span class="elgg-col">' . elgg_view('output/text', ['value' => $fee]) . elgg_view('output/longtext', ['value' => $event->fee_details]) . '</span>';
	$registration_details .= '</div>';
}

if ($type) {
	$registration_details .= '<div class="elgg-columns">';
	$registration_details .= '<label class="elgg-col">' . elgg_echo('event_manager:edit:form:type') . ':</label>';
	$registration_details .= '<span class="elgg-col">' . $type . '</span>';
	$registration_details .= '</div>';
}


$content = elgg_format_element('div', ['class' => 'elgg-rows'], $registration_details);
$content .= elgg_format_element('div', [], elgg_view('event_manager/event/rsvp', $vars));

$body = elgg_format_element('div', ['class' => 'event-manager-view-registration-details'], $content);

echo $body;
