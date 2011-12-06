<?php 
	$key = get_input('k');		
	$guid = get_input("guid");
	$user_guid = get_input('u_g', elgg_get_logged_in_user_guid());

	if($guid && ($entity = get_entity($guid)))
	{	
		if($entity instanceof Event)
		{
			$event = $entity;
		}
	}
	
	$save_to_pdf_link = elgg_view('page_elements/contentwrapper', array('body' => '<a href="'.$vars['url'].elgg_add_action_tokens_to_url('/action/event_manager/registration/pdf?k='.md5($event->time_created . get_site_secret() . $user_guid).'&guid='.$guid.'&u_g='.$user_guid).'">'.elgg_echo('event_manager:registration:view:savetopdf').' <img border="0" src="'.$vars['url'].'/mod/event_manager/_graphics/icons/pdf_icon.gif" /></a>'));
	
	if(!empty($key))
	{
		$tempKey = md5($event->time_created . get_site_secret() . $user_guid);
		
		if($event && ($tempKey == $key) && get_entity($user_guid))
		{
			echo elgg_view('page_elements/header');
			
			echo '<div style="width: 1000px; margin: 0 auto;">'.elgg_view_title(elgg_echo('event_manager:registration:yourregistration'));
			
			echo $save_to_pdf_link;

			echo elgg_view('event_manager/event/pdf', array('entity' => $event));
			
			elgg_set_ignore_access(true);

			echo $event->getRegistrationData($user_guid);

			elgg_set_ignore_access(false);

			if($event->with_program)
			{
				echo $event->getProgramData($user_guid);
			}

			echo elgg_view('page_elements/footer').'</div>';
		}
		else
		{
			forward(EVENT_MANAGER_BASEURL);
		}
	}
	else
	{
		gatekeeper();

		if($event)
		{
			if($event->canEdit() || ($user_guid == elgg_get_logged_in_user_guid()))
			{
				$title_text = elgg_echo('event_manager:registration:registrationto')."'".$event->title."'";

				$title = elgg_view_title($title_text);
				
				$output .=  $save_to_pdf_link;
				
				$output .= elgg_view('event_manager/event/pdf', array('entity' => $event));

				$output .= $event->getRegistrationData($user_guid);

				if($event->with_program)
				{
					$output .= $event->getProgramData($user_guid);
				}			

				if($user_guid == elgg_get_logged_in_user_guid())
				{
					if(EVENT_MANAGER_SITETAKEOVER)
					{
						$output .= '<br /><a style="margin-left: 10px;" href="/pg/event/register">'.elgg_echo('event_manager:registration:edityourregistration').'</a>';
					}
					else
					{
						$output .= '<br /><a style="margin-left: 10px;" href="'.EVENT_MANAGER_BASEURL.'/event/register/'.$event->getGUID().'/event_attending">'.elgg_echo('event_manager:registration:edityourregistration').'</a>';
					}
				}	

				$page_data = $title . $output;

				echo elgg_view_layout("sitetakeover", $page_data);
			}
			else
			{
				forward($event->getURL());
			}
		} 
		else 
		{
			register_error("no guid");
			forward(REFERER);
		}
	}