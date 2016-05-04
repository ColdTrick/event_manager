<?php

$event = elgg_extract('entity', $vars);
$can_edit = $event->canEdit();

$event_details = '';

// event details
if ($event->icontime) {
	$event_details .= "<div class='mbm elgg-border-plain center'>";
	$event_details .= elgg_view('output/img', [
		'src' => $event->getIconURL('master'),
		'alt' => $event->title,
	]);

	$event_details .= "</div>";
}

$start_day = $event->start_day;
$start_time = $event->start_time;
$end_ts = $event->end_ts;

$when_title = elgg_echo('date:weekday:' . date('w', $start_day)) . ', ';
$when_title .= elgg_echo('date:month:' . date('m', $start_day), [date('j', $start_day)]) . ' ';
$when_title .= date('Y', $start_day);

$when_subtitle = '';

if (!$end_ts) {
	$when_title .= ' ' . date('H:i', $start_time);
} else {
	if (date('d-m-Y', $end_ts) === date('d-m-Y', $start_day)) {
		// same day event
		$when_subtitle .= date('H:i', $start_time) . ' ' . strtolower(elgg_echo('to')) . ' ' . date('H:i', $end_ts);
	} else {
		$when_title .= ' ' . date('H:i', $start_time);
		$when_subtitle .= strtolower(elgg_echo('to')) . ' ';

		$when_subtitle .= elgg_echo('date:weekday:' . date('w', $end_ts)) . ', ';
		$when_subtitle .= elgg_echo('date:month:' . date('m', $end_ts), [date('j', $end_ts)]) . ' ';
		$when_subtitle .= date('Y', $end_ts) . ' ';
		$when_subtitle .= date('H:i', $end_ts);
	}
}

$when = "<div class='event-manager-event-when-title'>{$when_title}</div>";
if (!empty($when_subtitle)) {
	$when .= "<div class='event-manager-event-when-subtitle'>{$when_subtitle}</div>";
}

$event_details .= elgg_view_image_block(elgg_view_icon('calendar', ['class' => 'elgg-icon-hover']), $when, ['class' => 'event-manager-event-when']);

// description
$description = $event->description ?: $event->shortdescription;
if (!empty($description)) {
	$description_body = elgg_view('output/longtext', ['value' => $description, 'class' => 'man']);

	$event_details .= elgg_view_module('event', '', $description_body);
}

// location
$location_details = '';

$event_location = $event->location;
$event_venue = $event->venue;
$event_region = $event->region;

if ($event_region) {
	$location_details .= '<div class="clearfix">';
	$location_details .= '<label class="elgg-col elgg-col-1of5">' . elgg_echo('event_manager:edit:form:region') . ':</label>';
	$location_details .= '<span class="elgg-col elgg-col-4of5">' . $event_region . '</span>';
	$location_details .= '</div>';
}

if ($event_venue) {
	$location_details .= '<div class="clearfix">';
	$location_details .= '<label class="elgg-col elgg-col-1of5">' . elgg_echo('event_manager:edit:form:venue') . ':</label>';
	$location_details .= '<span class="elgg-col elgg-col-4of5">' . $event_venue . '</span>';
	$location_details .= '</div>';
}

if ($event_location) {
	$location_text = $event_location;
	$location_text .= elgg_view('output/url', [
		'href' => '//maps.google.com/maps?f=d&source=s_d&daddr=' . $event_location . '&hl=' . get_current_language(),
		'text' => elgg_echo('event_manager:event:location:plan_route'),
		'target' => '_blank',
		'class' => 'mlm',
	]);
	
	$location_details .= '<div class="clearfix">';
	$location_details .= '<label class="elgg-col elgg-col-1of5">' . elgg_echo('event_manager:edit:form:location') . ':</label>';
	$location_details .= '<span class="elgg-col elgg-col-4of5">' . $location_text . '</span>';
	$location_details .= '</div>';
	
	$location_details .= elgg_view('event_manager/event/maps/location', $vars);
}
if (!empty($location_details)) {
	$event_details .= elgg_view_module('event', '', $location_details);
}

// files
$event_files = elgg_view_menu('event_files', ['entity' => $event]);
if (!empty($event_files) || $can_edit) {
	$files_title = '';
	if ($can_edit) {
		$files_title .= elgg_view('output/url', [
			'href' => "events/event/upload/{$event->getGUID()}",
			'title' => elgg_echo('event_manager:event:uploadfiles'),
			'text' => elgg_view_icon('round-plus'),
			'class' => 'float-alt'
		]);
	}
	$files_title .= elgg_echo('event_manager:edit:form:files');
		
	if (empty($event_files)) {
		$event_files = elgg_echo('event_manager:event:uploadfiles:no_files');
	}

	$event_details .= elgg_view_module('info', $files_title, $event_files);
}

$registration_details = '';
$max_attendees = $event->max_attendees;
$fee = $event->fee;
$type = $event->event_type;
$endregistration_day = $event->endregistration_day;

if ($endregistration_day) {
	$registration_details .= '<div class="clearfix">';
	$registration_details .= '<label class="elgg-col elgg-col-1of5">' . elgg_echo('event_manager:edit:form:endregistration_day') . ':</label>';
	$registration_details .= '<span class="elgg-col elgg-col-4of5">' . event_manager_format_date($endregistration_day) . '</span>';
	$registration_details .= '</div>';
}

if ($max_attendees) {
	$registration_details .= '<div class="clearfix">';
	$registration_details .= '<label class="elgg-col elgg-col-1of5">' . elgg_echo('event_manager:edit:form:spots_left') . ':</label>';
	$registration_details .= '<span class="elgg-col elgg-col-4of5">';
	
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
		$registration_details .= $spots_left . " / " . $max_attendees;
	}

	$registration_details .= '</span>';
	$registration_details .= '</div>';
}

if ($fee) {
	$registration_details .= '<div class="clearfix">';
	$registration_details .= '<label class="elgg-col elgg-col-1of5">' . elgg_echo('event_manager:edit:form:fee') . ':</label>';
	$registration_details .= '<span class="elgg-col elgg-col-4of5">' . elgg_view('output/text', ['value' => $fee]) . '</span>';
	$registration_details .= '</div>';
}

if ($type) {
	$registration_details .= '<div class="clearfix">';
	$registration_details .= '<label class="elgg-col elgg-col-1of5">' . elgg_echo('event_manager:edit:form:type') . ':</label>';
	$registration_details .= '<span class="elgg-col elgg-col-4of5">' . $type . '</span>';
	$registration_details .= '</div>';
}

$registration_details .= elgg_view('event_manager/event/actions', $vars);

if (!empty($registration_details)) {
	$registration_title = elgg_echo('event_manager:registration:register:title');
	
	$event_details .= elgg_view_module('info', $registration_title, $registration_details);
}

echo $event_details;