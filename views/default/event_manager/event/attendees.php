<?php

	$event = $vars["entity"];
	
	$result = "";
	
	if($relationships = $event->getRelationships()){
		
		$ordered_relationships = array(
				EVENT_MANAGER_RELATION_ATTENDING,
				EVENT_MANAGER_RELATION_INTERESTED,
				EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST
				
			);
		
		if ($event->canEdit()) {
			$ordered_relationships[] = EVENT_MANAGER_RELATION_ATTENDING_PENDING;
		}
		
		foreach($ordered_relationships as $rel)	{
			if(($rel == EVENT_MANAGER_RELATION_ATTENDING) || ($rel == EVENT_MANAGER_RELATION_ATTENDING_PENDING) || $event->$rel || ($rel == EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST &&  $event->canEdit() && $event->waiting_list_enabled)){
				if(array_key_exists($rel, $relationships)){

					$members = $relationships[$rel];
					
					$rel_title = elgg_echo("event_manager:event:relationship:" . $rel . ":label") . " (" . count($members) . ")";
					
					$rel_content = "";
					foreach($members as $member){
						$member_entity = get_entity($member);
						$member_info = elgg_view_entity_icon($member_entity, "small", array("event" => $event));
						
						if($event->canEdit()){
							$rel = $member_entity->name;
							
							if($member_entity instanceof ElggUser){
								$rel .= " " . $member_entity->username;
							} else {
								$rel .= " " . $member_entity->email;
							}
							
							$member_info = "<span class='event-manager-event-view-attendee-info' rel='" . $rel . "'>" . $member_info . "</span>";
						}
						$rel_content .= $member_info;
						
					}
					
					$result .= elgg_view_module("info", $rel_title, $rel_content, array("class" => "event-manager-event-view-attendees"));
				}
			}
		}
		
		echo $result;
	}
	