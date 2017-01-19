<?php

$event = elgg_extract('entity', $vars);
$can_edit = $event->canEdit();

$event_details = '';

// event details
$event_banner_url = '';
if ($event->hasIcon('event_banner')) {
	$event_banner_url = $event->getIconURL('event_banner');
} elseif ($event->hasIcon('master')) {
	$event_banner_url = $event->getIconURL('master');
}
if (!empty($event_banner_url)) {
	$event_details .= "<div class='mbm elgg-border-plain event-manager-event-banner'>";
	$event_details .= elgg_view('output/img', [
		'src' => $event_banner_url,
		'alt' => $event->title,
	]);

	$event_details .= "</div>";
}
$event_start = $event->getStartTimestamp();
$event_end = $event->getEndTimestamp();

$when_title = elgg_echo('date:weekday:' . gmdate('w', $event_start)) . ', ';
$when_title .= elgg_echo('date:month:' . gmdate('m', $event_start), [gmdate('j', $event_start)]) . ' ';
$when_title .= gmdate('Y', $event_start);

$when_subtitle = '';

if (!$event_end) {
	$when_title .= ' ' . gmdate('H:i', $event_start);
} else {
	if (gmdate('d-m-Y', $event_end) === gmdate('d-m-Y', $event_start)) {
		// same day event
		$when_subtitle .= gmdate('H:i', $event_start) . ' ' . strtolower(elgg_echo('event_manager:date:to')) . ' ' . gmdate('H:i', $event_end);
	} else {
		$when_title .= ' ' . gmdate('H:i', $event_start);
		$when_subtitle .= strtolower(elgg_echo('event_manager:date:to')) . ' ';

		$when_subtitle .= elgg_echo('date:weekday:' . gmdate('w', $event_end)) . ', ';
		$when_subtitle .= elgg_echo('date:month:' . gmdate('m', $event_end), [gmdate('j', $event_end)]) . ' ';
		$when_subtitle .= gmdate('Y', $event_end) . ' ';
		$when_subtitle .= gmdate('H:i', $event_end);
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
	$event_details .= elgg_view_module('event', '', $location_details, ['id' => 'location']);
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
	$registration_details .= '<span class="elgg-col elgg-col-4of5">' . elgg_view('output/text', ['value' => $fee]) . elgg_view('output/longtext', ['value' => $event->fee_details]) . '</span>';
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
	
	$event_details .= elgg_view_module('info', $registration_title, $registration_details, ['class' => 'event-manager-forms-label-100']);
}

echo $event_details;