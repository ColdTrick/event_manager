<?php
	if($event = elgg_extract("entity", $vars)){
	
		$result = "";
	
		if($relationships = $event->getRelationships()){
	
			$ordered_relationships = array(
				EVENT_MANAGER_RELATION_PRESENTING,
				EVENT_MANAGER_RELATION_EXHIBITING,
				EVENT_MANAGER_RELATION_ORGANIZING,
			);
	
			foreach($ordered_relationships as $rel)	{
				if(array_key_exists($rel, $relationships)){

					$members = $relationships[$rel];

					$rel_title = elgg_echo("event_manager:event:relationship:" . $rel . ":label") . " (" . count($members) . ")";

					$rel_content = "";
					foreach($members as $member){
						$rel_content .= elgg_view_entity_icon(get_entity($member), "small", array("event" => $event));
					}

					$result .= elgg_view_module("aside", $rel_title, $rel_content, array("class" => "event-manager-event-view-attendees"));
				}
			}
	
			echo $result;
		}
	}