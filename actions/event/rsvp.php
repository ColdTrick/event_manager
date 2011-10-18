<?php 

	$guid = get_input("guid");
	$user_guid = get_input("user", get_loggedin_userid());
	
	if(!empty($guid) && $entity = get_entity($guid))
	{
		if($entity->getSubtype() == Event::SUBTYPE)
		{
			$event = $entity;
			
			if(($user = get_loggedin_user()) && ($rel = get_input("type")))
			{
				//echo '- loggedin and relation type is set<br />';
				if($rel == EVENT_MANAGER_RELATION_ATTENDING)
				{
					//echo '- relation type is \'attending\'<br />';
					if($event->hasEventSpotsLeft() && $event->hasSlotSpotsLeft())
					{
						//echo '- event and it\'s slots has spots left<br />';
						if($event->registration_needed)
						{
							$sitetakeover = event_manager_check_sitetakeover_event();
							if($sitetakeover['count']>0)
							{
								//echo '- forward to sitetakeover registration<br />';
								forward('/pg/event/register');
							}
							else
							{
								//echo '- forward to event registration<br />';
								forward(EVENT_MANAGER_BASEURL.'/event/register/'.$guid.'/'.$rel);
							}
						}
						else
						{
							//echo '- no registration needed, rsvp immediately<br />';
							$rsvp = $event->rsvp($rel, $user_guid);
						}						
					}
					else
					{
						//echo '- no spots left for this event, nor it\'s slots<br />';
						if($event->waiting_list_enabled)
						{
							$rel = EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST;
							//echo '- waiting list is enabled<br />';							
							if($event->openForRegistration())
							{
								//echo '- event is open for registration (datewise)<br />';	
								if($event->registration_needed)
								{							
									if($registration = $event->generateRegistrationForm())
									{
										//echo '- event CAN generate a registration form<br />';
										$sitetakeover = event_manager_check_sitetakeover_event();
										if($sitetakeover['count']>0)
										{
											//echo '- show site takeover waiting list<br />';
											forward('/pg/event/waitinglist');
										}
										else
										{
											//echo '- show normal event waiting list<br />';
											forward(EVENT_MANAGER_BASEURL.'/event/waitinglist/'.$guid);
										}
									}
									else
									{
										//echo '- cant generate registration form<br />';
										register_error(elgg_echo('event_manager:event:register:no_registrationform'));
									}
								}
								else
								{
									$rsvp = $event->rsvp($rel, $user_guid);
								}
							}
							else
							{
								//echo 'event is closed for registration, either the registration is set as ended, or the end date has been reached<br />';
								register_error(elgg_echo('event_manager:event:rsvp:registration_ended'));
							}
						}
						else
						{
							//echo '- waitinglist disabled, no registration form created, show error and forward back<br />';
							register_error(elgg_echo('event_manager:event:rsvp:nospotsleft'));
							forward(REFERER);
						}
					}
				}
				else
				{
					//echo '- relation ship type is not EVENT_MANAGER_RELATION_ATTENDING, rsvp otherwise<br />';
					if($event->$rel)
					{
						$rsvp = $event->rsvp($rel, $user_guid);
					}
					else
					{
						register_error(elgg_echo('event_manager:event:relationship:message:unavailable_relation'));
						forward(REFERER);
					}
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
		}
	}
	else
	{
		system_message(elgg_echo("no guid"));
		forward(REFERER);
	}
	exit;