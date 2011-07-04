<?php 

	$guid = get_input("guid");
	
	if(!empty($guid) && ($entity = get_entity($guid)))
	{
		if($entity instanceof Event)
		{
			$event = $entity;
			if($event && $event->canEdit())
			{
				elgg_extend_view('profile/menu/actions', 'event_manager/profile/menu/actions');
				
				$title_text = elgg_echo('event_manager:event:rsvp:waiting_list');
			
				$title = elgg_view_title($title_text . $back_text);
				
				if($waiting_list = $event->getEntitiesFromRelationship(EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST))
				{
					$content .= '<div class="event_manager_event_view_waitinglist">';
					foreach($waiting_list as $user)
					{
						$content .= elgg_view("profile/icon", array("entity" => $user, "size" => "small"));
					}
					$content .= '<div class="clearfloat"></div>';
					$content .= '</div>';
				}
				else
				{
					$content = elgg_echo('event_manager:event:waitinglist:empty');
				}
				
				$page_data = $title . elgg_view('page_elements/contentwrapper', array('body' => $content));
				
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