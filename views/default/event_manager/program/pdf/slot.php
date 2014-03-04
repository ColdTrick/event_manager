<?php

	$slot = $vars["entity"];
	$participate = $vars["participate"];
	$register_type = $vars["register_type"];
	
	$user_guid = $vars['user_guid'];
	if (empty($user_guid)) {
		$user_guid = elgg_get_logged_in_user_guid();
	}
	
	if(!empty($slot) && ($slot instanceof EventSlot)) {
		if ($user_guid) {
			if (check_entity_relationship($user_guid, EVENT_MANAGER_RELATION_SLOT_REGISTRATION, $slot->getGUID())) {
				
				$start_time = $slot->start_time;
				$end_time = $slot->end_time;
				
				$result = "<table><tr><td style='padding-left: 10px; vertical-align: top'>";
				$result .= date('H',$start_time) . ":" . date('i',$start_time) . " - " . date('H',$end_time) . ":" . date('i',$end_time);
				$result .= "</td><td>";
				$result .= "<b>" . $slot->title . "</b>";
				
				if ($location = $slot->location) {
					$result .= "<div>" . $location . "</div>";
				}
				$result .= "<div>" . elgg_view("output/text", array("value" => $slot->description)) . "</div>";
				
				$result .= "</td></tr></table>";
				
				echo $result;
			}
		}
	}
	