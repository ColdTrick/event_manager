<?php 
	
	function event_manager_sitetakeover_hook($hook, $entity_type, $returnvalue, $params)
	{
		$event = elgg_get_page_owner_entity();
		
		set_input('guid', $event->getGUID());
		
		include(dirname(dirname(__FILE__)) . "/pages/sitetakeover/view.php");
		
		return true;
	}
	
	function event_manager_user_hover_menu($hook, $entity_type, $returnvalue, $params){
		global $EVENT_MANAGER_ATTENDING_EVENT;
		
		$result = $returnvalue;
		
		if(!empty($EVENT_MANAGER_ATTENDING_EVENT)){
			$event = get_entity($EVENT_MANAGER_ATTENDING_EVENT);
			$user = elgg_extract("entity", $params);
			
			if($event->getOwnerGUID() != $user->getGUID()){
				$href = elgg_get_site_url() . 'action/event_manager/event/rsvp?guid=' . $EVENT_MANAGER_ATTENDING_EVENT . '&user=' . $user->getGUID() . '&type=' . EVENT_MANAGER_RELATION_UNDO;
				$href = elgg_add_action_tokens_to_url($href);
				
				$item = new ElggMenuItem("event_manager", elgg_echo("event_manager:event:relationship:kick"), $href);
				$item->setSection("action");
				
				$result[] = $item;
			}
		}
		
		return $result;
	}
	
	function event_manager_entity_menu($hook, $entity_type, $returnvalue, $params){
		$result = $returnvalue;
		
		if (elgg_in_context('widgets')) {
			return $result;
		}
		
		if(($handler = elgg_extract("handler", $params)) && ($handler == "event") && ($entity = elgg_extract("entity", $params))){
			
			if(!empty($result) && is_array($result)){
				foreach($result as &$item){
					switch($item->getName()){
						case "edit":
							$item->setHref(EVENT_MANAGER_BASEURL . "/event/edit/" . $entity->getGUID());
							break;
						case "delete":
							$href = elgg_get_site_url() . "action/event_manager/event/delete?guid=" . $entity->getGUID();
							$href = elgg_add_action_tokens_to_url($href);
							
							$item->setHref($href);
							break;
					}
				}
			}
		}
		
		return $result;
	}