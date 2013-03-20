<?php 
	
	function event_manager_user_hover_menu($hook, $entity_type, $returnvalue, $params){
		$result = $returnvalue;
		$event = false;
		
		$guid = get_input("guid");
		
		if(!empty($guid) && ($entity = get_entity($guid))){	
			if($entity->getSubtype() == Event::SUBTYPE) {
				$event = $entity;
			}
		}
		
		if($event){
			$user = elgg_extract("entity", $params);
			
			if($event->getOwnerGUID() != $user->getGUID()){
				$href = elgg_get_site_url() . 'action/event_manager/event/rsvp?guid=' . $event->getGUID() . '&user=' . $user->getGUID() . '&type=' . EVENT_MANAGER_RELATION_UNDO;
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
		
		if (elgg_in_context("widgets")) {
			return $result;
		}
		
		if (($entity = elgg_extract("entity", $params)) && elgg_instanceof($entity, "object", Event::SUBTYPE)) {
			$attendee_menu_options = array(
				"name" => "attendee_count", 
				"priority" => 50, 
				"text" => elgg_echo("event_manager:event:relationship:event_attending:entity_menu", array($entity->countAttendees())), 
				"href" => false
			);
			
			$result[] = ElggMenuItem::factory($attendee_menu_options);

			// change some of the basic menus
			if (!empty($result) && is_array($result)) {
				foreach ($result as &$item) {
					switch ($item->getName()) {
						case "edit":
							$item->setHref("events/event/edit/" . $entity->getGUID());
							break;
						case "delete":
							$href = elgg_get_site_url() . "action/event_manager/event/delete?guid=" . $entity->getGUID();
							$href = elgg_add_action_tokens_to_url($href);
							
							$item->setHref($href);
							break;
					}
				}
			}
			
			// show an unregister link for non logged in users
			if (!elgg_is_logged_in() && $entity->register_nologin) {
				$result[] = ElggMenuItem::factory(array(
					"name" => "unsubscribe",
					"text" => elgg_echo("event_manager:menu:unsubscribe"),
					"href" => "events/unsubscribe/" . $entity->getGUID() . "/" . elgg_get_friendly_title($entity->title),
					"priority" => 300
				));
			}
		}
		
		return $result;
	}
	
	/**
	 * add menu item to owner block
	 * 
	 * @param unknown_type $hook
	 * @param unknown_type $entity_type
	 * @param unknown_type $returnvalue
	 * @param unknown_type $params
	 */
	function event_manager_owner_block_menu($hook, $entity_type, $returnvalue, $params){
		$group = elgg_extract("entity", $params);
		if (elgg_instanceof($group, 'group') && $group->event_manager_enable != "no") {
			$url = '/events/event/list/' . $group->getGUID();
			$item = new ElggMenuItem('events', elgg_echo('event_manager:menu:group_events'), $url);
			$returnvalue[] = $item;
		}
		
		return $returnvalue;
	}
	
	/**
	 * Generates correct title link for widgets depending on the context
	 * 
	 * @param unknown_type $hook
	 * @param unknown_type $entity_type
	 * @param unknown_type $returnvalue
	 * @param unknown_type $params
	 * @return optional new link
	 */
	function event_manager_widget_events_url($hook, $entity_type, $returnvalue, $params){
		$result = $returnvalue;
		$widget = $params["entity"];
		
		if(empty($result) && ($widget instanceof ElggWidget) && $widget->handler == "events"){
			switch($widget->context){
				case "index":
					$result = "/events";
					break;
				case "groups":
					$result = "/events/event/list/" . $widget->getOwnerGUID();
					break;
				case "profile":
				case "dashboard":
					break;
			}				
		}
		return $result;
	}