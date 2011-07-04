<?php

	global $CONFIG;
					
	$title_text = elgg_echo("event_manager:registration:register:title");
	
	$guid = get_input("guid");
	$relation = get_input("relation");
	
	if(!empty($guid) && ($entity = get_entity($guid)))
	{
		if($entity->getSubtype() == Event::SUBTYPE)
		{
			$event = $entity;
		
			if(!isloggedin())
			{
				if(!$event->hasEventSpotsLeft() || !$event->hasSlotSpotsLeft())
				{
					if($event->waiting_list_enabled && $event->registration_needed && $event->openForRegistration())
					{
						forward(EVENT_MANAGER_BASEURL.'/event/waitinglist/'.$guid);
					}
					else
					{
						register_error(elgg_echo('event_manager:event:rsvp:nospotsleft'));
						forward(REFERER);
					}
				}
			}
				
			$form = $event->generateRegistrationForm();
			
			$back_text = '<div class="event_manager_back"><a href="'.$event->getURL().'">'.elgg_echo('event_manager:title:backtoevent').'</a></div>';
			
			$title = elgg_view_title($title_text . " '".$event->title."'" . $back_text);
						
			$page_data = $title . $form;
				
			$body = elgg_view_layout("two_column_left_sidebar", "", $page_data);
			
			page_draw($title_text, $body);
			
			$_SESSION['registerevent_values'] = null;
		}
	}
	else
	{
		system_message(elgg_echo("no guid"));
		forward(REFERER);
	}