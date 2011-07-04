<?php 

	$guid = get_input("guid");
	$user_guid = get_input("user", get_loggedin_userid());
	
	if(!empty($guid) && $entity = get_entity($guid))
	{
		if($entity->getSubtype() == Event::SUBTYPE)
		{
			$event = $entity;
			
			if($event && ($user = get_loggedin_user()) && ($rel = get_input("type")))
			{
				if($rel == EVENT_MANAGER_RELATION_ATTENDING)
				{
					if($event->hasEventSpotsLeft() && $event->hasSlotSpotsLeft())
					{
						if($event->registration_needed)
						{
							if($event->openForRegistration())
							{
								forward(EVENT_MANAGER_BASEURL.'/event/register/'.$guid.'/'.$rel);
							}
							else
							{
								register_error(elgg_echo('event_manager:event:rsvp:registration_ended'));
								forward(REFERER);
							}
						}
						else
						{
							$rsvp = $event->rsvp($rel, $user_guid);
						}
					}
					else
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
				else
				{
					$rsvp = $event->rsvp($rel, $user_guid);
				}
				
				if($rsvp)
				{
					system_message(elgg_echo('event_manager:event:relationship:message:'.$rel));
				} 
				else
				{
					register_error(elgg_echo('event_manager:event:relationship:message:error'));
				}
				
				forward(REFERER);				
			} 
			else 
			{
				register_error(elgg_echo('event_manager:event:relationship:message:error'));	
			}
		}
	}
	else
	{
		system_message(elgg_echo("no guid"));
		forward(REFERER);
	}