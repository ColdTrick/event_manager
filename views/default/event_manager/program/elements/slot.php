<?php 

	$slot = $vars["entity"];
	$participate = $vars["participate"];
	$register_type = $vars["register_type"];
	
	if(!empty($slot) && ($slot instanceof EventSlot)) {
		$result = "<table id='" . $slot->getGUID() . "'>";
	
		$result .= "<tr><td rowspan='2' class='event_manager_program_slot_attending'>";
		
		$slot_set = $slot->slot_set;
		
		$checkbox_options = array(
				"rel" => $slot_set,
				'name' => 'slotguid_'  .$slot->getGUID(), 
				'id' => 'slotguid_' . $slot->getGUID(),
				'value' => '1',
				'class' => 'event_manager_program_participatetoslot'
				
			);
		
		if(elgg_is_logged_in() && ($user_guid = elgg_get_logged_in_user_guid())) {
			if(check_entity_relationship($user_guid, EVENT_MANAGER_RELATION_SLOT_REGISTRATION, $slot->getGUID())) {
				if(!$participate) {
					$registered_for_slot = '<div title="' . elgg_echo("event_manager:event:relationship:event_attending") . '" class="event_manager_program_slot_attending_user"></div>';
				} else {
					$checkbox_options["checked"] = "checked";
					$registered_for_slot = elgg_view('input/checkbox', $checkbox_options);
				}
			} else {
				if($participate &&  ($slot->hasSpotsLeft() || $register_type == 'waitinglist')) {
					$registered_for_slot = elgg_view('input/checkbox', $checkbox_options);
				}
			}
		} else {
			if($participate && ($slot->hasSpotsLeft() || $register_type == 'waitinglist')) {
				$registered_for_slot = elgg_view('input/checkbox', $checkbox_options);
			} elseif(!empty($vars["member"]) && check_entity_relationship($vars["member"], EVENT_MANAGER_RELATION_SLOT_REGISTRATION, $slot->getGUID())){
				$registered_for_slot = '<div title="' . elgg_echo("event_manager:event:relationship:event_attending") . '" class="event_manager_program_slot_attending_user"></div>';
			}
		}
		
		if($registered_for_slot){
			$result .= $registered_for_slot;
		} else {
			$result .= "&nbsp;";
		}
		
		$start_time = $slot->start_time;
		$end_time = $slot->end_time;
		
		$result .= "</td><td class='event_manager_program_slot_time'>";
		$result .= date('H', $start_time) . ":" . date('i', $start_time) . " - " . date('H', $end_time) . ":" . date('i', $end_time);
		$result .= "</td><td class='event_manager_program_slot_details' rel='" . $slot->getGUID() . "'>";
		$result .= "<span><b>" . $slot->title . "</b></span>";
		
		if(!empty($slot_set)){
			$color = substr(sha1($slot_set, false), 0, 6);
			$result .= " <span class='event-manager-program-slot-set' style='background: #" . $color . "'>" . $slot_set . "</span>";
		}
		
		if($slot->canEdit() && !elgg_in_context('programmailview') && ($participate == false)) {
			$edit_slot = "<a href='#' class='event_manager_program_slot_edit' rel='" . $slot->getGUID() . "'>" . elgg_echo("edit") . "</a>";
			$delete_slot = "<a href='#' class='event_manager_program_slot_delete'>" . elgg_echo("delete") . "</a>";
			
			$result .= " [ " . $edit_slot . " | " . $delete_slot . " ]";
		}
		
		$subtitle_data = array();
		if($location = $slot->location) {
			$subtitle_data[] = $location;
		}
		
		if(!empty($slot->max_attendees)) {
			if(($slot->max_attendees > 0) && (($slot->max_attendees - $slot->countRegistrations()) > 0)) {
				$subtitle_data[] = ($slot->max_attendees - $slot->countRegistrations()) . ' ' . strtolower(elgg_echo('event_manager:edit:form:spots_left'));
			} else {
				$subtitle_data[] = strtolower(elgg_echo('event_manager:edit:form:spots_left:full'));
				
				$event = $slot->getEvent();
				if($event->waiting_list_enabled && $slot->getWaitingUsers(true)>0) {
					$subtitle_data[] = $slot->getWaitingUsers(true).elgg_echo('event_manager:edit:form:spots_left:waiting_list');
				} 
			}
		}
		
		if(!empty($subtitle_data)) {
			$result .= "<div class='elgg-quiet'>" . implode(" - ", $subtitle_data) . "</div>";
		}
		
		$result .= "</td></tr>";
		
		$result .= "<tr><td>";
		$result .= "&nbsp;";
		$result .= "</td><td>";
		$result .= "<div class='event_manager_program_slot_description'>" . elgg_view("output/text", array("value" => $slot->description)) . "</div>";
		$result .= "</td></tr>";
		
		$result .= "</table>";
		
		echo $result;
	}
