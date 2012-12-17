<?php 
	
	$event = $vars["entity"];
	$owner = $event->getOwnerEntity();
	$output = '';
	
	$actions = elgg_view("event_manager/event/actions", $vars);	
	
	if($event->icontime){
		$output .= '<div class="event_manager_event_view_image"><a href="' . $event->getIcon('master') . '" target="_blank"><img src="' . $event->getIcon('medium') . '" border="0" /></a></div>';
	}
	
	$output .= '<div class="event_manager_event_view_owner">'.elgg_echo('event_manager:event:view:createdby') . '</span> <a class="user" href="' . $owner->getURL().'">' . $owner->name . '</a> ' . elgg_view_friendly_time($event->time_created > 0 ? $event->time_created : $event->time_updated) . '</div>';
	
	$row_pattern = '<tr><td class="event-manager-event-details-labels"><b>%s</b>:</td><td>%s</td></tr>';
	// event details
	$event_details = "<table>";
	if($venue = $event->venue){
		$event_details .= sprintf($row_pattern, elgg_echo('event_manager:edit:form:venue'), $venue);
	}
	if($location = $event->getLocation()){
		$event_details .=  sprintf($row_pattern, elgg_echo('event_manager:edit:form:location'),
				'<a href="' . elgg_get_site_url() . 'events/event/route?from=' . $event->getLocation() . '" class="openRouteToEvent">' . $event->getLocation() . '</a>');
	}
	
	$event_details .= sprintf($row_pattern, elgg_echo('event_manager:edit:form:start_day') ,date(EVENT_MANAGER_FORMAT_DATE_EVENTDAY, $event->start_day));
	
	if(!$event->with_program){
		$event_details .= sprintf($row_pattern, elgg_echo('event_manager:edit:form:start_time'), date('H', $event->start_time) . ':' . date('i', $event->start_time));
	}
	
	// optional end day
	if($organizer = $event->organizer){
		$event_details .= sprintf($row_pattern, elgg_echo('event_manager:edit:form:organizer'), $organizer);
	}
	
	if($max_attendees = $event->max_attendees){
		
		$value='';
		$spots_left = ($max_attendees - $event->countAttendees());
		if($spots_left < 1) {
			$count_waitinglist = $event->countWaiters();
			if($count_waitinglist > 0){
				$value .= elgg_echo('event_manager:full') . ', ' . $count_waitinglist . ' ';
				if($count_waitinglist == 1) {
					$value .= elgg_echo('event_manager:personwaitinglist');
				} else {
					$value .= elgg_echo('event_manager:peoplewaitinglist');
				}
			} else {
				$value .= elgg_echo('event_manager:full');
			}
		} else {
			$value .= $spots_left . " / " . $max_attendees;
		}
		
		$event_details .= sprintf($row_pattern, elgg_echo('event_manager:edit:form:spots_left'),$value);
	}
	
	if($description = $event->description){
		$event_details .= sprintf($row_pattern, elgg_echo('event_manager:edit:form:description'), elgg_view("output/longtext", array("value" => $description)));
	}
	
	if($website = $event->website){
		$event_details .= sprintf($row_pattern, elgg_echo('event_manager:edit:form:website'), elgg_view("output/url", array("value" => $website)));
	}
	
	if($contact_details = $event->contact_details){
		$event_details .= sprintf($row_pattern, elgg_echo('event_manager:edit:form:contact_details'), elgg_view("output/text", array("value" => $contact_details)));
	}
	
	if($twitter_hash = $event->twitter_hash){
		$event_details .= sprintf($row_pattern, elgg_echo('event_manager:edit:form:twitter_hash'), elgg_view("output/text", array("value" => $twitter_hash)));
	}
	
	if($fee = $event->fee){
		$event_details .= sprintf($row_pattern, elgg_echo('event_manager:edit:form:fee'), elgg_view("output/text", array("value" => $fee)));
	}
	
	
	
	if($region = $event->region){
		$event_details .= sprintf($row_pattern, elgg_echo('event_manager:edit:form:region'), $region);
	}
	
	if($type = $event->event_type){
		$event_details .= sprintf($row_pattern, elgg_echo('event_manager:edit:form:type'), $type);
	}
	
	if($files = $event->hasFiles()){
		$user_path = 'events/' . $event->getGUID() . '/files/';
		$files = "<div class='event-manager-event-files'>";
		foreach($files as $file){
			$files .= '<a href="' . elgg_get_site_url() . 'events/event/file/' . $event->getGUID() . '/'. $file->file . '">' . elgg_view_icon("download", "mrs") . $file->title . '</a><br />';
		}
		
		$files .= '</div>';
		$event_details .= sprintf($row_pattern, elgg_echo('event_manager:edit:form:files'),$files);
		
	}
	
	$event_details .= "</table>";
	
	$output .= $event_details;
	
	$output .= '<div class="clearfloat"></div>';
	$output .= $actions;
		
	if($event->show_attendees){
		$output .= elgg_view("event_manager/event/attendees", $vars);
	}
	
	if($event->with_program){
		$output .= elgg_view("event_manager/program/view", $vars);
	}
	
	echo elgg_view_module("main", "", $output);
	
	if($event->comments_on){	
		echo elgg_view_comments($event);
	}