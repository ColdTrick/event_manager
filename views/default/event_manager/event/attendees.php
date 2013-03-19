<?php 

	$event = $vars["entity"];
	
	$result = "";
	
	if($relationships = $event->getRelationships()){
		
		$ordered_relationships = array(
				EVENT_MANAGER_RELATION_ATTENDING,
				EVENT_MANAGER_RELATION_INTERESTED,
				EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST
			);
		
		foreach($ordered_relationships as $rel)	{
			if(($rel == EVENT_MANAGER_RELATION_ATTENDING) || $event->$rel || ($rel == EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST &&  $event->canEdit() && $event->waiting_list_enabled)){
				if(array_key_exists($rel, $relationships)){

					$members = $relationships[$rel];
					
					$rel_title = elgg_echo("event_manager:event:relationship:" . $rel) . " (" . count($members) . ")";
					
					$rel_content = "";
					foreach($members as $member){
						$rel_content .= elgg_view_entity_icon(get_entity($member), "small", array("event" => $event));
					}
					
					$result .= elgg_view_module("info", $rel_title, $rel_content);
				}
			}
		}
		
		echo $result; 
	}
	