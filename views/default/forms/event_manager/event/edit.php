<?php

elgg_require_js('event_manager/edit_event');

// defaults
$fields = [
	'guid' => ELGG_ENTITIES_ANY_VALUE,
	'title' => ELGG_ENTITIES_ANY_VALUE,
	'shortdescription' => ELGG_ENTITIES_ANY_VALUE,
	'tags' => ELGG_ENTITIES_ANY_VALUE,
	'description' => ELGG_ENTITIES_ANY_VALUE,
	'comments_on' => 1,
	'venue' => ELGG_ENTITIES_ANY_VALUE,
	'location' => ELGG_ENTITIES_ANY_VALUE,
	'latitude' => ELGG_ENTITIES_ANY_VALUE,
	'longitude' => ELGG_ENTITIES_ANY_VALUE,
	'region' => ELGG_ENTITIES_ANY_VALUE,
	'event_type' => ELGG_ENTITIES_ANY_VALUE,
	'website' => ELGG_ENTITIES_ANY_VALUE,
	'contact_details' => ELGG_ENTITIES_ANY_VALUE,
	'fee' => ELGG_ENTITIES_ANY_VALUE,
	'twitter_hash' => ELGG_ENTITIES_ANY_VALUE,
	'organizer' => ELGG_ENTITIES_ANY_VALUE,
	'organizer_rsvp' => 0,
	'start_day' => date('Y-m-d', time()),
	'end_day' => ELGG_ENTITIES_ANY_VALUE,
	'start_time' => time(),
	'end_ts' => time() + 3600,
	'registration_ended' => ELGG_ENTITIES_ANY_VALUE,
	'endregistration_day' => ELGG_ENTITIES_ANY_VALUE,
	'with_program' => ELGG_ENTITIES_ANY_VALUE,
	'registration_needed' => ELGG_ENTITIES_ANY_VALUE,
	'register_nologin' => ELGG_ENTITIES_ANY_VALUE,
	'show_attendees' => 1,
	'notify_onsignup' => ELGG_ENTITIES_ANY_VALUE,
	'max_attendees' => ELGG_ENTITIES_ANY_VALUE,
	'waiting_list_enabled' => ELGG_ENTITIES_ANY_VALUE,
	'access_id' => get_default_access(),
	'container_guid' => elgg_get_page_owner_entity()->getGUID(),
	'event_interested' => 0,
	'event_presenting' => 0,
	'event_exhibiting' => 0,
	'event_organizing' => 0,
	'registration_completed' => ELGG_ENTITIES_ANY_VALUE,
];

$event = elgg_extract('entity', $vars);

if ($event) {
	// edit mode
	$fields['guid'] = $event->getGUID();
	$fields['location'] = $event->location;
	$fields['latitude'] = $event->getLatitude();
	$fields['longitude'] = $event->getLongitude();
	$fields['tags'] = string_to_tag_array($event->tags);

	foreach ($fields as $field => $value) {
		if (!in_array($field, ['guid', 'location', 'latitude', 'longitude'])) {
			$fields[$field] = $event->$field;
		}
	}

	// convert timestamp to date notation for correct display
	if (!empty($fields['start_day'])) {
		$fields['start_day'] = date('Y-m-d', $fields['start_day']);
	}
	if (empty($fields['end_ts'])) {
		$start_date = explode('-', $fields['start_day']);
		$fields['end_ts'] = mktime($fields['start_time_hours'], $fields['start_time_minutes'], 1, $start_date[1], $start_date[2], $start_date[0]) + 3600;
	}

	$fields['end_day'] = date('Y-m-d', $fields['end_ts']);
}

if (elgg_is_sticky_form('event')) {
	// merge defaults with sticky data
	$fields = array_merge($fields, elgg_get_sticky_values('event'));
}

elgg_clear_sticky_form('event');

$tabs = [
	[
		'text' => elgg_echo('event_manager:edit:form:tabs:profile'),
		'href' => '#event-manager-forms-event-edit-profile',
		'selected' => true,
	],
	[
		'text' => elgg_echo('event_manager:edit:form:tabs:location'),
		'href' => '#event-manager-forms-event-edit-location',
	],
	[
		'text' => elgg_echo('event_manager:edit:form:tabs:registration'),
		'href' => '#event-manager-forms-event-edit-registration',
	],
	[
		'text' => elgg_echo('event_manager:edit:form:tabs:extra'),
		'href' => '#event-manager-forms-event-edit-extra',
	],
];

$vars = array_merge($vars, $fields);

echo elgg_view('forms/event_manager/event/tabs/general', $vars);
echo elgg_view('navigation/tabs', [
	'id' => 'event-manager-forms-event-edit',
	'tabs' => $tabs,
	'class' => 'mtl',
]);

echo elgg_format_element('div', [
	'class' => 'event-tab',
	'id' => 'event-manager-forms-event-edit-profile',
], elgg_view('forms/event_manager/event/tabs/profile', $vars));

echo elgg_format_element('div', [
	'class' => 'event-tab hidden',
	'id' => 'event-manager-forms-event-edit-location',
], elgg_view('forms/event_manager/event/tabs/location', $vars));

echo elgg_format_element('div', [
	'class' => 'event-tab hidden',
	'id' => 'event-manager-forms-event-edit-registration',
], elgg_view('forms/event_manager/event/tabs/registration', $vars));

echo elgg_format_element('div', [
	'class' => 'event-tab hidden',
	'id' => 'event-manager-forms-event-edit-extra',
], elgg_view('forms/event_manager/event/tabs/extra', $vars));

$hidden = elgg_view('output/url', [
	'href' => "ajax/view/event_manager/event/maps/select_location?guid={$fields["guid"]}",
	'text' => 'google maps',
	'id' => 'openGoogleMaps',
	'class' => 'hidden',
]);

$hidden .= elgg_view_input('hidden', ['name' => 'latitude', 'id' => 'event_latitude', 'value' => $fields['latitude']]);
$hidden .= elgg_view_input('hidden', ['name' => 'longitude', 'id' => 'event_longitude', 'value' => $fields['longitude']]);
$hidden .= elgg_view_input('hidden', ['name' => 'guid', 'value' => $fields['guid']]);
$hidden .= elgg_view_input('hidden', ['name' => 'container_guid', 'value' => $fields['container_guid']]);

$submit_input = elgg_view_input('submit', ['value' => elgg_echo('save')]);

echo elgg_format_element('div', ['class' => 'elgg-foot mtl'], $hidden . $submit_input);
