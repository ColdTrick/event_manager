<?php

elgg_require_js('event_manager/edit_event');

// defaults
$fields = array(
	"guid" => ELGG_ENTITIES_ANY_VALUE,
	"title" => ELGG_ENTITIES_ANY_VALUE,
	"shortdescription" => ELGG_ENTITIES_ANY_VALUE,
	"tags" => ELGG_ENTITIES_ANY_VALUE,
	"description" => ELGG_ENTITIES_ANY_VALUE,
	"comments_on" => 1,
	"venue" => ELGG_ENTITIES_ANY_VALUE,
	"location" => ELGG_ENTITIES_ANY_VALUE,
	"latitude" => ELGG_ENTITIES_ANY_VALUE,
	"longitude" => ELGG_ENTITIES_ANY_VALUE,
	"region" => ELGG_ENTITIES_ANY_VALUE,
	"event_type" => ELGG_ENTITIES_ANY_VALUE,
	"website" => ELGG_ENTITIES_ANY_VALUE,
	"contact_details" => ELGG_ENTITIES_ANY_VALUE,
	"fee" => ELGG_ENTITIES_ANY_VALUE,
	"twitter_hash" => ELGG_ENTITIES_ANY_VALUE,
	"organizer" => ELGG_ENTITIES_ANY_VALUE,
	"organizer_rsvp" => 0,
	"start_day" => date('Y-m-d', time()),
	"end_day" => ELGG_ENTITIES_ANY_VALUE,
	"start_time" => time(),
	"end_ts" => time() + 3600,
	"registration_ended" => ELGG_ENTITIES_ANY_VALUE,
	"endregistration_day" => ELGG_ENTITIES_ANY_VALUE,
	"with_program" => ELGG_ENTITIES_ANY_VALUE,
	"registration_needed" => ELGG_ENTITIES_ANY_VALUE,
	"register_nologin" => ELGG_ENTITIES_ANY_VALUE,
	"show_attendees" => 1,
	"hide_owner_block" => ELGG_ENTITIES_ANY_VALUE,
	"notify_onsignup" => ELGG_ENTITIES_ANY_VALUE,
	"max_attendees" => ELGG_ENTITIES_ANY_VALUE,
	"waiting_list_enabled" => ELGG_ENTITIES_ANY_VALUE,
	"access_id" => get_default_access(),
	"container_guid" => elgg_get_page_owner_entity()->getGUID(),
	"event_interested" => 0,
	"event_presenting" => 0,
	"event_exhibiting" => 0,
	"event_organizing" => 0,
	"registration_completed" => ELGG_ENTITIES_ANY_VALUE,
);

$event = elgg_extract('entity', $vars);

if ($event) {
	// edit mode
	$fields["guid"] = $event->getGUID();
	$fields["location"] = $event->location;
	$fields["latitude"] = $event->getLatitude();
	$fields["longitude"] = $event->getLongitude();
	$fields["tags"] = string_to_tag_array($event->tags);

	foreach ($fields as $field => $value) {
		if (!in_array($field, array("guid", "location", "latitude", "longitude"))) {
			$fields[$field] = $event->$field;
		}
	}

	// convert timestamp to date notation for correct display
	if (!empty($fields["start_day"])) {
		$fields["start_day"] = date('Y-m-d', $fields["start_day"]);
	}
	if (empty($fields["end_ts"])) {
		$start_date = explode('-', $fields["start_day"]);
		$fields["end_ts"] = mktime($fields["start_time_hours"], $fields["start_time_minutes"], 1, $start_date[1],$start_date[2],$start_date[0]) + 3600;
	}

	$fields["end_day"] = date('Y-m-d', $fields["end_ts"]);
}

if (elgg_is_sticky_form('event')) {
	// merge defaults with sticky data
	$fields = array_merge($fields, elgg_get_sticky_values('event'));
}

elgg_clear_sticky_form('event');

$tabs = array(
	array(
		"text" => elgg_echo("event_manager:edit:form:tabs:profile"),
		"href" => "#event-manager-forms-event-edit-profile",
		"selected" => true
	),
	array(
		"text" => elgg_echo("event_manager:edit:form:tabs:location"),
		"href" => "#event-manager-forms-event-edit-location",
	),
	array(
		"text" => elgg_echo("event_manager:edit:form:tabs:registration"),
		"href" => "#event-manager-forms-event-edit-registration",
	),
	array(
		"text" => elgg_echo("event_manager:edit:form:tabs:extra"),
		"href" => "#event-manager-forms-event-edit-extra",
	),
);
$tabs_body = elgg_view("navigation/tabs", array(
	"id" => "event-manager-forms-event-edit",
	"tabs" => $tabs,
));

$vars = array_merge($vars, $fields);

$hidden = elgg_view('output/url', [
	'href' => "ajax/view/event_manager/event/maps/select_location?guid={$fields["guid"]}",
	'text' => "google maps",
	'id' => "openGoogleMaps",
	'class' => 'hidden',
]);

$hidden .= elgg_view('input/hidden', array('name' => 'latitude', 'id' => 'event_latitude', 'value' => $fields["latitude"]));
$hidden .= elgg_view('input/hidden', array('name' => 'longitude', 'id' => 'event_longitude', 'value' => $fields["longitude"]));
$hidden .= elgg_view('input/hidden', array('name' => 'guid', 'value' => $fields["guid"]));
$hidden .= elgg_view('input/hidden', array('name' => 'container_guid', 'value' => $fields["container_guid"]));

$general_body = elgg_view('forms/event_manager/event/tabs/general', $vars);
$profile_body = elgg_view('forms/event_manager/event/tabs/profile', $vars);
$location_body = elgg_view('forms/event_manager/event/tabs/location', $vars);
$registration_body = elgg_view('forms/event_manager/event/tabs/registration', $vars);
$extra_body = elgg_view('forms/event_manager/event/tabs/extra', $vars);

$required_fields = elgg_echo('requiredfields');

$submit_input = elgg_view('input/submit', array(
	'value' => elgg_echo('save')
));

$form_body = <<<HTML
	<fieldset>$general_body</fieldset>
	<br /><br />
	$tabs_body
	<fieldset class="event-tab" id="event-manager-forms-event-edit-profile">$profile_body</fieldset>
	<fieldset class="event-tab hidden" id="event-manager-forms-event-edit-location">$location_body</fieldset>
	<fieldset class="event-tab hidden" id="event-manager-forms-event-edit-registration">$registration_body</fieldset>
	<fieldset class="event-tab hidden" id="event-manager-forms-event-edit-extra">$extra_body</fieldset>
	<br />
	<fieldset>
		$hidden
		<div>$submit_input</div>
		<div class="elgg-text-help">* $required_fields</div>
	</fieldset>
HTML;

$form = elgg_view('input/form', array(
	'id' => 'event_manager_event_edit',
	'name' 	=> 'event_manager_event_edit',
	'action' => '/action/event_manager/event/edit',
	'enctype' => 'multipart/form-data',
	'body' => $form_body,
));

echo elgg_view_module("main", "", $form);
