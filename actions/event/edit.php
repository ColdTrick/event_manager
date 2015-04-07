<?php
	
	// start a new sticky form session in case of failure
	elgg_make_sticky_form('event');

	$guid = get_input("guid");
	$container_guid = get_input("container_guid");
	$title = get_input("title");
	$shortdescription = get_input("shortdescription");
	$tags = get_input("tags");
	$twitter_hash = get_input("twitter_hash");
	$organizer = get_input("organizer");
	$organizer_rsvp = get_input("organizer_rsvp");
	$description = get_input("description");
	$comments_on = get_input("comments_on");
	$location = get_input("location");
	$region = get_input("region");
	$event_type = get_input("event_type");
	$website = get_input("website");
	$contact_details = get_input("contact_details");
	$latitude = get_input("latitude");
	$longitude = get_input("longitude");
	$venue = get_input("venue");
	$fee = get_input("fee");
	$start_day = get_input("start_day");
	$end_day = get_input("end_day");
	$end_time_hours = get_input("end_time_hours");
	$end_time_minutes = get_input("end_time_minutes");
	$registration_ended = get_input("registration_ended");
	$show_attendees = get_input("show_attendees");
	$hide_owner_block = get_input("hide_owner_block");
	$notify_onsignup = get_input("notify_onsignup");
	$endregistration_day = get_input("endregistration_day");
	$max_attendees = get_input("max_attendees");
	$waiting_list = get_input("waiting_list");
	$access_id = get_input("access_id");
	$with_program = get_input("with_program");
	$delete_current_icon = get_input("delete_current_icon");
	$registration_needed = get_input("registration_needed");
	$register_nologin = get_input("register_nologin");
	
	$event_interested = get_input("event_interested");
	$event_presenting = get_input("event_presenting");
	$event_exhibiting = get_input("event_exhibiting");
	$event_organizing = get_input("event_organizing");
	
	$registration_completed = get_input("registration_completed");
	
	$waiting_list_enabled = get_input("waiting_list_enabled");
	
	$start_time_hours = get_input("start_time_hours");
	$start_time_minutes = get_input("start_time_minutes");
	$start_time = mktime($start_time_hours, $start_time_minutes, 1, 0, 0, 0);
	
	if (!empty($end_day)) {
		$end_date = explode('-', $end_day);
		$end_ts = mktime($end_time_hours, $end_time_minutes, 1, $end_date[1], $end_date[2], $end_date[0]);
	}
	
	$forward_url = REFERER;
	
	if(!empty($start_day)) {
		$date = explode('-',$start_day);
		$start_day = mktime(0,0,1,$date[1],$date[2],$date[0]);
		
		$start_ts = mktime($start_time_hours, $start_time_minutes, 1, $date[1], $date[2], $date[0]);
		
		if (!empty($end_ts) && ($end_ts < $start_ts)) {
			register_error("End time has to be after start time");
			forward(REFERER);
		}
	}

	if(!empty($endregistration_day)) {
		$date_endregistration_day = explode('-',$endregistration_day);
		$endregistration_day = mktime(0,0,1,$date_endregistration_day[1],$date_endregistration_day[2],$date_endregistration_day[0]);
	}
	
	if(!empty($guid) && $entity = get_entity($guid)) {
		if($entity->getSubtype() == Event::SUBTYPE) {
			$event = $entity;
		}
	}
	
	if($event_type == '-') {
		$event_type = '';
	}
	
	if($region == '-') {
		$region = '';
	}
	
	if(!empty($tags)) {
		$tags = string_to_tag_array($tags);
	}
	
	if(!empty($max_attendees) && !is_numeric($max_attendees)) {
		$max_attendees = "";
	}
	
	if(!empty($title) && !empty($start_day) && !empty($end_ts)) {
		$newEvent = false;
		if (!isset($event)) {
			$newEvent = true;
			$event = new Event();
		}
		
		$event->title = $title;
		$event->description = $description;
		$event->container_guid = $container_guid;
		$event->access_id = $access_id;
		$event->save();
		
		$event->setLocation($location);
		$event->setLatLong($latitude, $longitude);
		$event->tags = $tags;
		
		if ($newEvent) {
			// add event create river event
			add_to_river('river/object/event/create', 'create', elgg_get_logged_in_user_guid(), $event->getGUID());
			
			// add optional organizer relationship
			if ($organizer_rsvp) {
				$event->rsvp(EVENT_MANAGER_RELATION_ORGANIZING, null, true, false);
			}
		}
		
		$event->shortdescription = $shortdescription;
		$event->comments_on = $comments_on;
		$event->registration_ended = $registration_ended;
		$event->registration_needed = $registration_needed;
		$event->show_attendees = $show_attendees;
		$event->hide_owner_block = $hide_owner_block;
		$event->notify_onsignup = $notify_onsignup;
		$event->max_attendees = $max_attendees;
		$event->waiting_list = $waiting_list;
		$event->venue = $venue;
		$event->twitter_hash = $twitter_hash;
		$event->contact_details = $contact_details;
		$event->region = $region;
		$event->website = $website;
		$event->event_type = $event_type;
		$event->organizer = $organizer;
		$event->fee = $fee;
		$event->start_day = $start_day;
		$event->start_time = $start_time;
		
		if (!empty($end_ts)) {
			$event->end_ts = $end_ts;
		}
		
		$event->with_program = $with_program;
		$event->endregistration_day = $endregistration_day;
		$event->register_nologin = $register_nologin;
		
		$event->event_interested = $event_interested;
		$event->event_presenting = $event_presenting;
		$event->event_exhibiting = $event_exhibiting;
		$event->event_organizing = $event_organizing;
		
		$event->waiting_list_enabled = $waiting_list_enabled;
		$event->registration_completed = $registration_completed;
				
		$eventDays = $event->getEventDays();
		if ($with_program && !$eventDays) {
			$eventDay = new EventDay();
			$eventDay->title = 'Event day 1';
			$eventDay->container_guid = $event->getGUID();
			$eventDay->owner_guid = $event->getGUID();
			$eventDay->access_id = $event->access_id;
			$eventDay->save();
			$eventDay->date = $event->start_day;
			$eventDay->addRelationship($event->getGUID(), 'event_day_relation');
			
			$eventSlot = new EventSlot();
			$eventSlot->title = 'Activity title';
			$eventSlot->description = 'Activity description';
			$eventSlot->container_guid = $event->container_guid;
			$eventSlot->owner_guid = $event->owner_guid;
			$eventSlot->access_id = $event->access_id;
			$eventSlot->save();
			
			$eventSlot->location = $event->location;
			$eventSlot->start_time = '08:00';
			$eventSlot->end_time = '09:00';
			$eventSlot->addRelationship($eventDay->getGUID(), 'event_day_slot_relation');
		}

		$event->setAccessToOwningObjects($access_id);
		
		$prefix = "events/".$event->guid."/";
		
		if (($icon_file = get_resized_image_from_uploaded_file("icon", 100, 100)) && ($icon_sizes = elgg_get_config("icon_sizes"))) {
			// create icon
				
			$fh = new ElggFile();
			$fh->owner_guid = $event->getOwnerGUID();
				
			foreach ($icon_sizes as $icon_name => $icon_info) {
				if ($icon_file = get_resized_image_from_uploaded_file("icon", $icon_info["w"], $icon_info["h"], $icon_info["square"], $icon_info["upscale"])) {
					$fh->setFilename($prefix . $icon_name . ".jpg");
						
					if($fh->open("write")){
						$fh->write($icon_file);
						$fh->close();
					}
				}
			}
				
			$event->icontime = time();
		} elseif ($delete_current_icon) {
			if ($icon_sizes = elgg_get_config("icon_sizes")) {
				$fh = new ElggFile();
				$fh->owner_guid = $event->getOwnerGUID();
					
				foreach ($icon_sizes as $name => $info) {
					$fh->setFilename($prefix . $name . ".jpg");
			
					if($fh->exists()){
						$fh->delete();
					}
				}
			}
			
			unset($event->icontime);
		}
		
		// added because we need an update event
		if ($event->save()) {
			// remove sticky form entries
			elgg_clear_sticky_form('event');
			
			system_message(elgg_echo("event_manager:action:event:edit:ok"));
			$forward_url = $event->getURL();
		}
	} else {
		register_error(elgg_echo("event_manager:action:event:edit:error_fields"));
	}
	
	forward($forward_url);
	