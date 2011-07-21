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
		$form = $event->generateRegistrationForm();
		
		$title = elgg_view_title($title_text . " '".$event->title."'");
					
		$page_data = $title . $form;
				
		echo elgg_view_layout("sitetakeover", $page_data);	
	} 
	else 
	{
		register_error("no guid");
		forward(REFERER);
	}