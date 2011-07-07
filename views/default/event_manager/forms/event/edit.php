<?php 

	// defaults
	$fields = array(
			"guid" 					=> ELGG_ENTITIES_ANY_VALUE,
			"title" 				=> ELGG_ENTITIES_ANY_VALUE,
			"shortdescription" 		=> ELGG_ENTITIES_ANY_VALUE,
			"tags" 					=> ELGG_ENTITIES_ANY_VALUE,
			"description" 			=> ELGG_ENTITIES_ANY_VALUE,
			"comments_on"			=> 1,
			"venue"					=> ELGG_ENTITIES_ANY_VALUE,
			"location"				=> ELGG_ENTITIES_ANY_VALUE,
			"latitude"				=> ELGG_ENTITIES_ANY_VALUE,
			"longitude"				=> ELGG_ENTITIES_ANY_VALUE,
			"region"				=> ELGG_ENTITIES_ANY_VALUE,
			"event_type"			=> ELGG_ENTITIES_ANY_VALUE,
			"organizer"				=> ELGG_ENTITIES_ANY_VALUE,
			"start_day" 			=> time(),
			"start_time" 			=> ELGG_ENTITIES_ANY_VALUE,
			"end_time" 				=> ELGG_ENTITIES_ANY_VALUE,
			"registration_ended" 	=> ELGG_ENTITIES_ANY_VALUE,
			"endregistration_day" 	=> ELGG_ENTITIES_ANY_VALUE,
			"with_program" 			=> ELGG_ENTITIES_ANY_VALUE,
			"registration_needed" 	=> ELGG_ENTITIES_ANY_VALUE,
			"register_nologin" 		=> ELGG_ENTITIES_ANY_VALUE,
			"show_attendees"		=> 1,
			"notify_onsignup"		=> ELGG_ENTITIES_ANY_VALUE,
			"max_attendees"		 	=> ELGG_ENTITIES_ANY_VALUE,
			"waiting_list_enabled"	=> ELGG_ENTITIES_ANY_VALUE,
			"access_id"				=> get_default_access(),
			"container_guid"		=> page_owner_entity()->getGUID(),
			/*"event_attending"		=> 1,
			"event_interested"		=> 1,
			"event_presenting"		=> 1,
			"event_exhibiting"		=> 1,
			"event_organizing"		=> 1,*/
		);
		
	$region_options = event_manager_event_region_options();
	$type_options = event_manager_event_type_options();
	
	if($event = $vars['entity'])
	{
		// edit mode
		$fields["guid"]			= $event->getGUID();
		$fields["location"]		= $event->getLocation();
		$fields["latitude"]		= $event->getLatitude();
		$fields["longitude"]	= $event->getLongitude();
		$fields["tags"]			= array_reverse(string_to_tag_array($event->tags));
		
		if($event->icontime)
		{
			$currentIcon = '<img src="'.$event->getIcon().'" />';
		}
		
		foreach($fields as $field => $value){
			if(!in_array($field, array("guid", "location", "latitude", "longitude"))){
				$fields[$field] = $event->$field;
			}
		}
		
		
		
		$fields["start_time_hours"] = date('H', $event->start_time);
		$fields["start_time_minutes"] = date('i', $event->start_time);	
	
		$fields["end_time_hours"] = date('H', $event->end_time);
		$fields["end_time_minutes"] = date('i', $event->end_time);
	}
	else
	{
		
		$start_time_hours = '';
		$start_time_minutes = '';
		
		$end_time_hours = '';
		$end_time_minutes = '';
		
		// new mode
		if(!empty($_SESSION['createevent_values']))
		{
			// check for empty fields that should revert to defaults
			foreach(array("start_day", "access_id") as $data){
				if($_SESSION['createevent_values'][$data] == ''){
					unset($_SESSION['createevent_values'][$data]);
				}
			}
			
			// merge defaults with session data
			$fields = array_merge($fields, $_SESSION['createevent_values']);
		}
	}
	
	$form_body .= 	'<a style="display: none;" href="'.EVENT_MANAGER_BASEURL.'/event/googlemaps" id="openGoogleMaps">google maps</a>';
	$form_body .= 	elgg_view('input/hidden', array('internalname' => 'latitude', 'internalid' => 'event_latitude', 'value' => $fields["latitude"]));
	$form_body .= 	elgg_view('input/hidden', array('internalname' => 'longitude', 'internalid' => 'event_longitude', 'value' => $fields["longitude"]));
	$form_body .= 	elgg_view('input/hidden', array('internalname' => 'guid', 'value' => $fields["guid"]));
	$form_body .= 	elgg_view('input/hidden', array('internalname' => 'container_guid', 'value' => $fields["container_guid"]));
	
	$form_body .= "<table>";
	
	$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('event_manager:edit:form:title') . " *</td><td>" . elgg_view('input/text', array('internalname' => 'title', 'value' => $fields["title"])) . "</td></tr>";

	$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('event_manager:edit:form:shortdescription') . " *</td><td>" . elgg_view('input/text', array('internalname' => 'shortdescription', 'value' => $fields["shortdescription"])) . "</td></tr>";
	
	$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('tags') . " *</td><td>" . elgg_view('input/tags', array('internalname' => 'tags', 'value' => $fields["tags"])) . "</td></tr>";

	$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('event_manager:edit:form:description') . "</td><td>" . elgg_view('input/longtext', array('internalname' => 'description', 'value' => $fields["description"])) . "</td></tr>";

	$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('event_manager:edit:form:icon') . "</td><td>" . elgg_view('input/file', array('internalname' => 'icon')) . "</td></tr>";
	
	if($currentIcon)
	{
		$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('event_manager:edit:form:currenticon') . "</td><td>".$currentIcon."<br />".
		elgg_view('input/checkboxes', array('internalname' => 'delete_current_icon', 'internalid' => 'delete_current_icon', 'value' => 0, 'options' => 
		array(elgg_echo('event_manager:edit:form:delete_current_icon')=>'1')))."</td></tr>";
	}
	
	$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('event_manager:edit:form:start_day') . " *</td><td>" . elgg_view('input/event_manager_datepicker', array('internalname' => 'start_day', 'internalid' => 'start_day', 'value' => date(EVENT_MANAGER_FORMAT_DATE_EVENTDAY, $fields["start_day"]))) . "</td></tr>";
	
	if($fields["with_program"])
	{
		$hidden = " class='hidden' ";
	}
	
	$form_body .= "<tr{$hidden} id='hide_start_time'><td class='event_manager_event_edit_label'>" . elgg_echo("event_manager:edit:form:start_time") . "</td><td>" . event_manager_get_form_pulldown_hours('start_time_hours', $fields["start_time_hours"]).event_manager_get_form_pulldown_minutes('start_time_minutes', $fields["start_time_minutes"]) . "</td></tr>";
	$form_body .= "<tr{$hidden} id='hide_end_time'><td class='event_manager_event_edit_label'>" . elgg_echo("event_manager:edit:form:end_time") . "</td><td>" . event_manager_get_form_pulldown_hours('end_time_hours', $fields["end_time_hours"]).event_manager_get_form_pulldown_minutes('end_time_minutes', $fields["end_time_minutes"]) . "</td></tr>";
	
	$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('event_manager:edit:form:organizer') . "</td><td>" . elgg_view('input/text', array('internalname' => 'organizer', 'value' => $fields["organizer"])) . "</td></tr>";

	$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('event_manager:edit:form:venue') . "</td><td>" . elgg_view('input/text', array('internalname' => 'venue', 'value' => $fields["venue"])) . "</td></tr>";
	
	if(event_manager_has_maps_key())
	{
		$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('event_manager:edit:form:location') . "</td><td>" . elgg_view('input/text', array('internalname' => 'location', 'internalid' => 'openmaps', 'value' => $fields["location"], 'readonly' => true)) . "</td></tr>";
	}
	else
	{
		$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('event_manager:edit:form:location') . "</td><td>" . elgg_view('input/text', array('internalname' => 'location', 'internalid' => 'location', 'value' => $fields["location"])) . "</td></tr>";
	}
	
	if($region_options)
	{
		$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('event_manager:edit:form:region') . "</td><td>" . elgg_view('input/pulldown', array('internalname' => 'region', 'value' => $fields["region"], 'options' => $region_options)) . "</td></tr>";
	}
	
	if($type_options)
	{ 
		$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('event_manager:edit:form:type') . "</td><td>" . elgg_view('input/pulldown', array('internalname' => 'event_type', 'value' => $fields["event_type"], 'options' => $type_options)) . "</td></tr>";
	} 

	$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('event_manager:edit:form:max_attendees') . "</td><td>" . elgg_view('input/text', array('internalname' => 'max_attendees', 'value' => $fields["max_attendees"])) . "</td></tr>";

	$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('event_manager:edit:form:options') . "</td><td>";
	
		$form_body .=	elgg_view('input/checkboxes', array('internalname' => 'with_program', 'internalid' => 'with_program', 'value' => $fields["with_program"], 'options' => array(elgg_echo('event_manager:edit:form:with_program')=>'1')));
		$form_body .= 	elgg_view('input/checkboxes', array('internalname' => 'comments_on', 'value' => $fields["comments_on"], 'options' => array(elgg_echo('event_manager:edit:form:comments_on')=>'1')));
		$form_body .= 	elgg_view('input/checkboxes', array('internalname' => 'notify_onsignup', 'value' => $fields["notify_onsignup"], 'options' => array(elgg_echo('event_manager:edit:form:notify_onsignup')=>'1')));
		$form_body .= 	elgg_view('input/checkboxes', array('internalname' => 'registration_needed', 'value' => $fields["registration_needed"], 'options' => array(elgg_echo('event_manager:edit:form:registration_needed')=>'1')));
		$form_body .= 	elgg_view('input/checkboxes', array('internalname' => 'show_attendees', 'value' => $fields["show_attendees"], 'options' => array(elgg_echo('event_manager:edit:form:show_attendees')=>'1')));
		$form_body .= 	elgg_view('input/checkboxes', array('internalname' => 'waiting_list_enabled', 'value' => $fields["waiting_list_enabled"], 'options' => array(elgg_echo('event_manager:edit:form:waiting_list')=>'1')));
		$form_body .= 	elgg_view('input/checkboxes', array('internalname' => 'register_nologin', 'value' => $fields["register_nologin"], 'options' => array(elgg_echo('event_manager:edit:form:register_nologin')=>'1')));
	
	$form_body .= "</td></tr>";
	
	$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('event_manager:edit:form:endregistration_day') . "</td><td>";
	
	$form_body .= elgg_view('input/event_datepicker', array('internalname' => 'endregistration_day', 'internalid' => 'endregistration_day', 'value' => (($fields["endregistration_day"]!=0)?date(EVENT_MANAGER_FORMAT_DATE_EVENTDAY,$fields["endregistration_day"]):''))) . "<br />";
	$form_body .= elgg_view('input/checkboxes', array('internalname' => 'registration_ended', 'value' => $fields["registration_ended"], 'options' => array(elgg_echo('event_manager:edit:form:registration_ended')=>'1')));
	
	$form_body .= "</td></tr><tr><td>&nbsp</td></tr>";
	
	/*$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('event_manager:edit:form:rsvp_options') . "</td><td>";
	
		$form_body .= elgg_view('input/checkboxes', array('internalname' => 'event_attending', 'internalid' => 'event_attending', 'value' => $fields["rsvp_attending"], 'options' => array(elgg_echo('event_manager:event:relationship:event_attending')=>'1')));
		$form_body .= elgg_view('input/checkboxes', array('internalname' => 'event_interested', 'internalid' => 'event_interested', 'value' => $fields["rsvp_interested"], 'options' => array(elgg_echo('event_manager:event:relationship:event_interested')=>'1')));
		$form_body .= elgg_view('input/checkboxes', array('internalname' => 'event_presenting', 'internalid' => 'event_presenting', 'value' => $fields["rsvp_presenting"], 'options' => array(elgg_echo('event_manager:event:relationship:event_presenting')=>'1')));
		$form_body .= elgg_view('input/checkboxes', array('internalname' => 'event_exhibiting', 'internalid' => 'event_exhibiting', 'value' => $fields["rsvp_exhibiting"], 'options' => array(elgg_echo('event_manager:event:relationship:event_exhibiting')=>'1')));
		$form_body .= elgg_view('input/checkboxes', array('internalname' => 'event_organizing', 'internalid' => 'event_organizing', 'value' => $fields["rsvp_organizing"], 'options' => array(elgg_echo('event_manager:event:relationship:event_organizing')=>'1')));
	
	$form_body .= "</td></tr>";*/
	
	$form_body .= "<tr><td class='event_manager_event_edit_label'>" . elgg_echo('access') . "</td><td>" . elgg_view('input/access', array('internalname' => 'access_id', 'value' => $fields["access_id"])) . "</td></tr>";
	
	$form_body .= "</table>";
					
	$form_body .= 	elgg_view('input/submit', array('value' => elgg_echo('save')));
	$form_body .= 	'<div class="event_manager_required">(* = '.elgg_echo('requiredfields').')</div>';
	
	$form = 		elgg_view('input/form', array(	'internalid' 	=> 'event_manager_event_edit', 
													'internalname' 	=> 'event_manager_event_edit', 
													'action' 		=> $vars['url'].'action/event_manager/event/edit', 
													'enctype' 		=> 'multipart/form-data', 
													'body' 			=> $form_body));
	
	echo elgg_view("page_elements/contentwrapper", array("body" => $form));
	
	// unset sticky data
	$_SESSION['createevent_values'] = null;