<?php

// start a new sticky form session in case of failure
elgg_make_sticky_form('event');

$title = get_input('title');

$event_start = (int) get_input('event_start');
$start_time_hours = (int) get_input('start_time_hours');
$start_time_minutes = (int) get_input('start_time_minutes');

$event_end = (int) get_input('event_end');
$end_time_hours = (int) get_input('end_time_hours');
$end_time_minutes = (int) get_input('end_time_minutes');

$endregistration_day = get_input('endregistration_day');

$access_id = (int) get_input('access_id');

if (empty($title) || empty($event_start) || empty($event_end)) {
	register_error(elgg_echo('event_manager:action:event:edit:error_fields'));
	forward(REFERER);
}

$event_end += $end_time_minutes * 60;
$event_end += $end_time_hours * 3600;


$event_start += $start_time_minutes * 60;
$event_start += $start_time_hours * 3600;

if ($event_end < $start_ts) {
	register_error(elgg_echo('event_manager:action:event:edit:end_before_start'));
	forward(REFERER);
}

if (!empty($endregistration_day)) {
	$date_endregistration_day = explode('-', $endregistration_day);
	$endregistration_day = mktime(0, 0, 1, $date_endregistration_day[1], $date_endregistration_day[2], $date_endregistration_day[0]);
}

$entity = get_entity(get_input('guid'));
$event_created = false;
if ($entity instanceof \Event) {
	$event = $entity;
} else {
	$event_created = true;
	$event = new \Event();
}

$event->title = $title;
$event->description = get_input('description');
$event->container_guid = (int) get_input('container_guid');
$event->access_id = $access_id;
$event->save();

$event->setLocation(get_input('location'));
$event->setLatLong(get_input('latitude'), get_input('longitude'));

$event->tags = string_to_tag_array(get_input('tags'));

if ($event_created) {
	elgg_create_river_item([
		'view' => 'river/object/event/create',
		'action_type' => 'create',
		'subject_guid' => elgg_get_logged_in_user_guid(),
		'object_guid' => $event->getGUID(),
	]);
}

$event->setMaxAttendees(get_input('max_attendees'));
$event->setRegion(get_input('region'));
$event->setEventType(get_input('event_type'));

$event->event_start = $event_start;
$event->event_end = $event_end;

$event->with_program = get_input('with_program');
$event->endregistration_day = $endregistration_day;

$metadata_fields = [
	'shortdescription', 'comments_on', 'registration_ended', 'registration_needed', 'show_attendees',
	'notify_onsignup', 'waiting_list', 'venue', 'contact_details', 'website',
	'organizer', 'fee', 'fee_details', 'register_nologin', 'waiting_list_enabled', 'registration_completed',
	'event_interested', 'event_presenting', 'event_exhibiting',
];

foreach ($metadata_fields as $field) {
	$event->{$field} = get_input($field);
}

$event->generateInitialProgramData();

$event->setAccessToOwningObjects($access_id);

$icon_sizes = elgg_get_config('icon_sizes');
$icon_sizes['event_banner'] = ['w' => 1920, 'h' => 1080, 'square' => false, 'upscale' => false];

$icon_file = get_resized_image_from_uploaded_file('icon', 100, 100);

if ($icon_file) {
	// create icons

	$fh = new \ElggFile();
	$fh->owner_guid = $event->guid;

	foreach ($icon_sizes as $icon_name => $icon_info) {
		$icon_file = get_resized_image_from_uploaded_file('icon', $icon_info['w'], $icon_info['h'], $icon_info['square'], $icon_info['upscale']);

		if ($icon_file) {
			$fh->setFilename("{$icon_name}.jpg");

			if ($fh->open('write')) {
				$fh->write($icon_file);
				$fh->close();
			}
		}
	}

	$event->icontime = time();
} elseif (get_input('delete_current_icon')) {
	$event->deleteIcon();
}

// added because we need an update event
$event->save();

// remove sticky form entries
elgg_clear_sticky_form('event');

system_message(elgg_echo('event_manager:action:event:edit:ok'));

forward($event->getURL());
