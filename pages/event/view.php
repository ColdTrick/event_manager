<?php 
	$guid = get_input("guid");
	
	if(!empty($guid) && ($entity = get_entity($guid)))
	{	
		if($entity->getSubtype() == Event::SUBTYPE)
		{
			$event = $entity;
		}
	}
	
	if($event)
	{		
		set_page_owner($event->getContainer());
		 
		$title_text = $event->title;
		
		$title = elgg_view_title($title_text);
		
		$output = elgg_view_entity($event, true);
		$page_data = $title . $output;
		
		$body = elgg_view_layout("two_column_left_sidebar", "", $page_data);
		
		page_draw($title_text, $body);		
	} 
	else 
	{
		register_error("geen guid");
		forward(REFERER);
	}