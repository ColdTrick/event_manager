<?php
	
	gatekeeper();

	$guid = get_input('guid');
		
	if(!empty($guid) && ($entity = get_entity($guid)))
	{
		if($entity instanceof Event)
		{
			$event = $entity;
			
			$title_text = elgg_echo("event_manager:registration:programparticipation");
			
			if($event->with_program)
			{
				$content = $event->getProgramData(get_loggedin_userid(), true);
				
				$content .= elgg_view('input/button', array('type' => 'button', 'internalid' => 'event_manager_save_program_participation', 'value' => elgg_echo('save')));
			}
		
			$back_text = '<div class="event_manager_back"><a href="'.$event->getURL().'">'.elgg_echo('event_manager:title:backtoevent').'</a></div>';
			$title = elgg_view_title($title_text . $back_text);
			
			$body = elgg_view('page_elements/contentwrapper', array('body' => $content));	
			
			$page_data = $title . $body;
							
			$content = elgg_view_layout("two_column_left_sidebar", "", $page_data);
			
			page_draw($title_text, $content);
		}
		else
		{
			forward(EVENT_MANAGER_BASEURL);
		}
	}
	else
	{
		register_error(elgg_echo('no guid'));
		forward(EVENT_MANAGER_BASEURL);
	}