<?php 

if(!isloggedin())
{
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
		$title_text = elgg_echo('login');
		
		$title = elgg_view_title($title_text);
		
		$output = elgg_view('sitetakeover/login');
		
		$page_data = $title . $output;
		
		echo elgg_view_layout("sitetakeover", $page_data);	
	} 
	else 
	{
		register_error("no guid");
		forward(REFERER);
	}
}
else
{
	forward('pg/event/view');
}