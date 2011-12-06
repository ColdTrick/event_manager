<?php 
	
	$guid = get_input("guid");
	
	if(!empty($guid) && ($entity = get_entity($guid))){	
		if($entity->getSubtype() == Event::SUBTYPE) {
			$event = $entity;
		}
	}
	
	if($event){		
		elgg_set_page_owner_guid($event->getContainerGUID());
		 
		$title_text = $event->title;
		elgg_push_breadcrumb($title_text);
		
		$output = elgg_view_entity($event, array("full_view" => true));
		
		$body = elgg_view_layout('one_sidebar', array(
			'filter' => '',
			'content' => $output,
			'title' => $title_text,
		));
		
		echo elgg_view_page($title_text, $body);
		
	} else {
		register_error(elgg_echo("InvalidParameterException:GUIDNotFound", array($guid)));
		forward(REFERER);
	}