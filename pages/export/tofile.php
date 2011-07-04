<?php

	$guid = get_input("guid");
	
	if($entity = get_entity($guid))
	{	
		if($entity->getSubtype() == Event::SUBTYPE)
		{
			$event = $entity;
		}
	}
	
	if($event && $event->canEdit())
	{
		
		/*header("Content-Type: text/csv");
		header("Content-Disposition: Attachment; filename=export.csv");
		header("Cache-Control: no-cache");
		header("Pragma: no-cache");
		
		echo event_manager_export_attendees($event, true);
		exit;*/		
	} 
	else 
	{
		register_error("geen guid");
		forward(REFERER);
	}