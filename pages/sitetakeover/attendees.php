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
		$title_text = elgg_echo('event_manager:event:attendees');
		
		$title = elgg_view_title($title_text);
		
		$output = elgg_view("event_manager/event/attendees", array('entity' => $event));
		
		$page_data = $title . $output;
		
		echo elgg_view_layout("sitetakeover", $page_data);	
	} 
	else 
	{
		register_error("no guid");
		forward(REFERER);
	}