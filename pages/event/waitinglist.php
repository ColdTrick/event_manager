<?php 

	$guid = get_input("guid");
	
	if(!empty($guid) && ($entity = get_entity($guid)))
	{
		if($entity instanceof Event)
		{
			$event = $entity;
			if($event)
			{
				if(!$event->waiting_list_enabled)
				{
					//system_message(elgg_echo('event_manager:registration:message:registrationnotneeded'));
					forward($event->getURL());
				}
				
				if(!$event->openForRegistration())
				{
					register_error(elgg_echo('event_manager:event:rsvp:registration_ended'));
					forward($event->getURL());
				}
				$title_text = elgg_echo('event_manager:event:rsvp:waiting_list');
				
				$back_text = '<div class="event_manager_back"><a href="'.$event->getURL().'">'.elgg_echo('event_manager:title:backtoevent').'</a></div>';
				
				$title = elgg_view_title($title_text . " '".$event->title."'" . $back_text);
				
				$form = $event->generateRegistrationForm('waitinglist');
				
				$page_data = $title . elgg_view('page_elements/contentwrapper', array('body' => $form));
				
				$body = elgg_view_layout("two_column_left_sidebar", "", $page_data);
				
				page_draw($title_text, $body);	
			}
		}
	}
	else
	{
		system_message(elgg_echo("no guid"));
		forward(REFERER);
	}