<?php 

	$event = $vars["entity"];

	$options = array();
	
	$context = get_context();
	
	if($event->canEdit() && $context !== "widget") {
		if($tools = elgg_view("event_manager/event/tools", $vars)){
			$options[] = $tools;
		}	
	}
	
	if(isloggedin()){
		if($rsvp = elgg_view("event_manager/event/rsvp", $vars)){
			$options[] = $rsvp;
		}

		if(!in_array($context, array("widget", "maps"))){
			if($registration = elgg_view("event_manager/event/registration", $vars)){
				$options[] = $registration;
			}
		}		
	}
	else
	{
		if($event->register_nologin)
		{
			if(event_manager_check_sitetakeover_event())
			{
				$register_link = '/pg/event/register';
			}
			else 
			{
				$register_link = EVENT_MANAGER_BASEURL . '/event/register/'.$event->getGUID();
			}
			$options[] = '<a class="event_manager_register_link" href="' . $register_link .'">'.elgg_echo('event_manager:event:register:register_link').'</a>';
		}
	}

	if(empty($vars["full"]) && $event->show_attendees){
		$attending_count = 0;
		if($count = $event->getRelationships(true)){
			if(array_key_exists(EVENT_MANAGER_RELATION_ATTENDING, $count))
			{
				$attending_count = $count[EVENT_MANAGER_RELATION_ATTENDING];
			} 
		}
		
		$options[] = $attending_count . " ". strtolower(elgg_echo("event_manager:event:relationship:event_attending"));
	}	
	
	echo implode(" | ", $options);