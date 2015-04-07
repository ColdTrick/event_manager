<?php

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
			"start_day" => date(EVENT_MANAGER_FORMAT_DATE_EVENTDAY, time()),
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
		
	$region_options = event_manager_event_region_options();
	$type_options = event_manager_event_type_options();
	
	if ($event = $vars['entity']) {
		// edit mode
		$fields["guid"] = $event->getGUID();
		$fields["location"] = $event->getLocation();
		$fields["latitude"] = $event->getLatitude();
		$fields["longitude"] = $event->getLongitude();
		$fields["tags"] = string_to_tag_array($event->tags);
		
		if ($event->icontime) {
			$currentIcon = '<img src="' . $event->getIcon() . '" />';
		}
		
		foreach ($fields as $field => $value) {
			if (!in_array($field, array("guid", "location", "latitude", "longitude"))) {
				$fields[$field] = $event->$field;
			}
		}
		
		// convert timestamp to date notation for correct display
		if (!empty($fields["start_day"])) {
			$fields["start_day"] = date(EVENT_MANAGER_FORMAT_DATE_EVENTDAY, $fields["start_day"]);
		}
		if (empty($fields["end_ts"])) {
			$start_date = explode('-', $fields["start_day"]);
			$fields["end_ts"] = mktime($fields["start_time_hours"], $fields["start_time_minutes"], 1, $start_date[1],$start_date[2],$start_date[0]) + 3600;
		}
		
		$fields["end_day"] = date(EVENT_MANAGER_FORMAT_DATE_EVENTDAY, $fields["end_ts"]);
	}
	
	if (elgg_is_sticky_form('event')) {
		// merge defaults with sticky data
		$fields = array_merge($fields, elgg_get_sticky_values('event'));
	}
	
	elgg_clear_sticky_form('event');
	
	$form_body = '<a class="hidden" href="' . elgg_get_site_url() . 'events/event/googlemaps/' . $fields["guid"] . '" id="openGoogleMaps">google maps</a>';
	$form_body .= elgg_view('input/hidden', array('name' => 'latitude', 'id' => 'event_latitude', 'value' => $fields["latitude"]));
	$form_body .= elgg_view('input/hidden', array('name' => 'longitude', 'id' => 'event_longitude', 'value' => $fields["longitude"]));
	$form_body .= elgg_view('input/hidden', array('name' => 'guid', 'value' => $fields["guid"]));
	$form_body .= elgg_view('input/hidden', array('name' => 'container_guid', 'value' => $fields["container_guid"]));
	
	$form_body .= "<table>";
	
	$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('title') . " *</td><td>" . elgg_view('input/text', array('name' => 'title', 'value' => $fields["title"])) . "</td></tr>";
	
	$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('event_manager:edit:form:start') . " *</td>";
	$form_body .= "<td>" . elgg_view('input/date', array('name' => 'start_day', 'id' => 'start_day', 'value' => $fields["start_day"], "class" => "event_manager_event_edit_date")) . " ";
	$form_body .= event_manager_get_form_pulldown_hours('start_time_hours', date('H', $fields["start_time"]));
	$form_body .= event_manager_get_form_pulldown_minutes('start_time_minutes', date('i', $fields["start_time"])) . "</td></tr>";
	
	$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('event_manager:edit:form:end') . " *</td>";
	$form_body .= "<td>" . elgg_view('input/date', array('name' => 'end_day', 'id' => 'end_day', 'value' => $fields["end_day"], "class" => "event_manager_event_edit_date")) . " ";
	$form_body .= event_manager_get_form_pulldown_hours('end_time_hours', date('H', $fields["end_ts"]));
	$form_body .= event_manager_get_form_pulldown_minutes('end_time_minutes', date('i', $fields["end_ts"])) . "</td></tr>";
	
	$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('event_manager:edit:form:shortdescription') . "</td><td>" . elgg_view('input/text', array('name' => 'shortdescription', 'value' => $fields["shortdescription"])) . "</td></tr>";
	
	$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('description') . "</td><td>" . elgg_view('input/longtext', array('name' => 'description', 'value' => $fields["description"])) . "</td></tr>";

	$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('tags') . "</td><td>" . elgg_view('input/tags', array('name' => 'tags', 'value' => $fields["tags"])) . "</td></tr>";
	
	$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('event_manager:edit:form:icon') . "</td><td>" . elgg_view('input/file', array('name' => 'icon')) . "</td></tr>";
	
	if (!empty($currentIcon)) {
		$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('event_manager:edit:form:currenticon') . "</td><td>".$currentIcon."<br />".
		elgg_view('input/checkboxes', array('name' => 'delete_current_icon', 'id' => 'delete_current_icon', 'value' => 0, 'options' =>
		array(elgg_echo('event_manager:edit:form:delete_current_icon')=>'1')))."</td></tr>";
	}

	$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('event_manager:edit:form:organizer') . "</td><td>" . elgg_view('input/text', array('name' => 'organizer', 'value' => $fields["organizer"]));
	if (!$event) {
		$form_body .= "<br/>" . elgg_view('input/checkboxes', array('name' => 'organizer_rsvp', 'id' => 'organizer_rsvp', 'value' => $fields["organizer_rsvp"], 'options' => array(elgg_echo('event_manager:edit:form:organizer_rsvp')=>'1')));
	}
	
	$form_body .= "</td></tr>";

	$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('event_manager:edit:form:venue') . "</td><td>" . elgg_view('input/text', array('name' => 'venue', 'value' => $fields["venue"])) . "</td></tr>";
	
	$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('event_manager:edit:form:location') . "</td><td>" . elgg_view('input/text', array('name' => 'location', 'id' => 'openmaps', 'value' => $fields["location"], 'readonly' => true)) . "</td></tr>";
	
	if ($region_options)	{
		$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('event_manager:edit:form:region') . "</td><td>" . elgg_view('input/dropdown', array('name' => 'region', 'value' => $fields["region"], 'options' => $region_options)) . "</td></tr>";
	}
	
	if ($type_options) {
		$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('event_manager:edit:form:type') . "</td><td>" . elgg_view('input/dropdown', array('name' => 'event_type', 'value' => $fields["event_type"], 'options' => $type_options)) . "</td></tr>";
	}

	$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('event_manager:edit:form:contact_details') . "</td><td>" . elgg_view('input/text', array('name' => 'contact_details', 'value' => $fields["contact_details"])) . "</td></tr>";
	
	$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('event_manager:edit:form:website') . "</td><td>" . elgg_view('input/url', array('name' => 'website', 'value' => $fields["website"])) . "</td></tr>";

	$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('event_manager:edit:form:twitter_hash') . "</td><td>" . elgg_view('input/text', array('name' => 'twitter_hash', 'value' => $fields["twitter_hash"])) . "</td></tr>";
	
	$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('event_manager:edit:form:fee') . "</td><td>" . elgg_view('input/text', array('name' => 'fee', 'value' => $fields["fee"])) . "</td></tr>";

	$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('event_manager:edit:form:max_attendees') . "</td><td>" . elgg_view('input/text', array('name' => 'max_attendees', 'value' => $fields["max_attendees"])) . "</td></tr>";

	$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('event_manager:edit:form:options') . "</td><td>";
	
	$form_body .=	elgg_view('input/checkboxes', array('name' => 'with_program', 'id' => 'with_program', 'value' => $fields["with_program"], 'options' => array(elgg_echo('event_manager:edit:form:with_program')=>'1')));
	$form_body .= 	elgg_view('input/checkboxes', array('name' => 'comments_on', 'value' => $fields["comments_on"], 'options' => array(elgg_echo('event_manager:edit:form:comments_on')=>'1')));
	$form_body .= 	elgg_view('input/checkboxes', array('name' => 'notify_onsignup', 'value' => $fields["notify_onsignup"], 'options' => array(elgg_echo('event_manager:edit:form:notify_onsignup')=>'1')));
	$form_body .= 	elgg_view('input/checkboxes', array('name' => 'registration_needed', 'value' => $fields["registration_needed"], 'options' => array(elgg_echo('event_manager:edit:form:registration_needed')=>'1')));
	$form_body .= 	elgg_view('input/checkboxes', array('name' => 'show_attendees', 'value' => $fields["show_attendees"], 'options' => array(elgg_echo('event_manager:edit:form:show_attendees')=>'1')));
	$form_body .= 	elgg_view('input/checkboxes', array('name' => 'hide_owner_block', 'value' => $fields["hide_owner_block"], 'options' => array(elgg_echo('event_manager:edit:form:hide_owner_block')=>'1')));
	$form_body .= 	elgg_view('input/checkboxes', array('name' => 'waiting_list_enabled', 'value' => $fields["waiting_list_enabled"], 'options' => array(elgg_echo('event_manager:edit:form:waiting_list')=>'1')));
	$form_body .= 	elgg_view('input/checkboxes', array('name' => 'register_nologin', 'value' => $fields["register_nologin"], 'options' => array(elgg_echo('event_manager:edit:form:register_nologin')=>'1')));
	
	$form_body .= "</td></tr>";
	
	$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('event_manager:edit:form:endregistration_day') . "</td><td>";
	
	$form_body .= elgg_view('input/date', array('name' => 'endregistration_day', 'id' => 'endregistration_day', 'value' => (($fields["endregistration_day"]!=0)?date(EVENT_MANAGER_FORMAT_DATE_EVENTDAY,$fields["endregistration_day"]):''))) . "<br />";
	$form_body .= elgg_view('input/checkboxes', array('name' => 'registration_ended', 'value' => $fields["registration_ended"], 'options' => array(elgg_echo('event_manager:edit:form:registration_ended')=>'1')));
	
	$form_body .= "</td></tr><tr><td>&nbsp</td></tr>";
	
	$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('event_manager:edit:form:rsvp_options') . "</td><td>";
	
	$form_body .= elgg_view('input/checkboxes', array('name' => 'event_interested', 'id' => 'event_interested', 'value' => $fields["event_interested"], 'options' => array(elgg_echo('event_manager:event:relationship:event_interested')=>'1')));
	$form_body .= elgg_view('input/checkboxes', array('name' => 'event_presenting', 'id' => 'event_presenting', 'value' => $fields["event_presenting"], 'options' => array(elgg_echo('event_manager:event:relationship:event_presenting')=>'1')));
	$form_body .= elgg_view('input/checkboxes', array('name' => 'event_exhibiting', 'id' => 'event_exhibiting', 'value' => $fields["event_exhibiting"], 'options' => array(elgg_echo('event_manager:event:relationship:event_exhibiting')=>'1')));
	$form_body .= elgg_view('input/checkboxes', array('name' => 'event_organizing', 'id' => 'event_organizing', 'value' => $fields["event_organizing"], 'options' => array(elgg_echo('event_manager:event:relationship:event_organizing')=>'1')));
	
	$form_body .= "</td></tr>";
	
	$form_body .= "<tr>";
	$form_body .= "<td class='event_manager_event_edit_label'>" . elgg_echo('event_manager:edit:form:registration_completed') . "</td>";
	$form_body .= "<td>";
	$form_body .= elgg_view('input/longtext', array('name' => 'registration_completed', 'value' => $fields["registration_completed"]));
	$form_body .= "<div class='elgg-subtext'>" . elgg_echo("event_manager:edit:form:registration_completed:description") . "</div>";
	$form_body .= "</td>";
	$form_body .= "</tr>";
	
	$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('access') . "</td><td>" . elgg_view('input/access', array('name' => 'access_id', 'value' => $fields["access_id"])) . "</td></tr>";
	
	$form_body .= "</table>";
					
	$form_body .= elgg_view('input/submit', array('value' => elgg_echo('save')));
	$form_body .= '<div class="event_manager_required">(* = '.elgg_echo('requiredfields').')</div>';
	
	$form = elgg_view('input/form', array(
									'id' => 'event_manager_event_edit',
									'name' 	=> 'event_manager_event_edit',
									'action' => '/action/event_manager/event/edit',
									'enctype' => 'multipart/form-data',
									'body' => $form_body
									));
	
	echo elgg_view_module("main", "", $form);
	
	// unset sticky data TODO: replace with sticky forms functionality
// 	$_SESSION['createevent_values'] = null;