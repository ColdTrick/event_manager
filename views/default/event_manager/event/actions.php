<?php 

	$event = $vars["entity"];

	$options = array();
	
	if($event->canEdit() && get_context() !== "widget") {
		if($tools = elgg_view("event_manager/event/tools", $vars)){
			$options[] = $tools;
		}	
	}
	
	if(isloggedin()){
		if($rsvp = elgg_view("event_manager/event/rsvp", $vars)){
			$options[] = $rsvp;
		}

		if(!in_array(get_context(), array("widget", "maps"))){
			if($registration = elgg_view("event_manager/event/registration", $vars)){
				$options[] = $registration;
			}
		}		
	}
	else
	{
		if($event->register_nologin)
		{
			echo '<a href="' . EVENT_MANAGER_BASEURL . '/event/register/'.$event->getGUID().'">'.elgg_echo('event_manager:event:register:register_link').'</a> | ';
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
		
		$options[] = "<a href='" . $event->getURL() . "'>" . $attending_count . " ". strtolower(elgg_echo("event_manager:event:relationship:event_attending")) . "</a>";
	}
	
	
	echo implode(" | ", $options);
	
?>