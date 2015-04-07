<?php
	
	$guid = get_input("guid");
	
	if(!empty($guid) && ($entity = get_entity($guid))){
		if($entity->getSubtype() == Event::SUBTYPE) {
			$event = $entity;
		}
	}
	
	if($event){

		// add export button
		
		elgg_load_js("addthisevent");
		elgg_register_menu_item("title", ElggMenuItem::factory(array(
			"name" => "addthisevent",
			"href" => false,
			"text" => elgg_view("event_manager/event/addthisevent", array("entity" => $event)))));
		
		elgg_set_page_owner_guid($event->getContainerGUID());
		$page_owner = elgg_get_page_owner_entity();
		if($page_owner instanceof ElggGroup){
			elgg_push_breadcrumb($page_owner->name, "/events/event/list/" . $page_owner->getGUID());
		}
		
		$title_text = $event->title;
		elgg_push_breadcrumb($title_text);
		
		$output = elgg_view_entity($event, array("full_view" => true));
		
		$sidebar = elgg_view("event_manager/event/sidebar", array("entity" => $event));
		
		$body = elgg_view_layout('content', array(
			'filter' => '',
			'content' => $output,
			'title' => $title_text,
			'sidebar' => $sidebar
		));
		
		echo elgg_view_page($title_text, $body);
		
	} else {
		register_error(elgg_echo("InvalidParameterException:GUIDNotFound", array($guid)));
		forward(REFERER);
	}