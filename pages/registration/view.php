<?php 
	$key = get_input('k');		
	$guid = get_input("guid");
	$user_guid = get_input('u_g', get_loggedin_userid());

	if($guid && ($entity = get_entity($guid)))
	{	
		if($entity instanceof Event)
		{
			$event = $entity;
		}
	}

	if(!empty($key))
	{
		$tempKey = md5($event->time_created . get_site_secret() . $user_guid);
		
		if($event && ($tempKey == $key) && get_entity($user_guid))
		{
			echo elgg_view('page_elements/header');

			echo '<div style="width: 1000px; margin: 0 auto;">'.elgg_view_title(elgg_echo('event_manager:registration:yourregistration'));

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
			if($event->canEdit() || ($user_guid == get_loggedin_userid()))
			{
				$title_text = elgg_echo('event_manager:registration:registrationto')."'".$event->title."'";

				$back_text = '<div class="event_manager_back"><a href="'.$event->getURL().'">'.elgg_echo('event_manager:title:backtoevent').'</a></div>';

				$title = elgg_view_title($title_text . $back_text);				

				$output .= $event->getRegistrationData($user_guid);

				if($event->with_program)
				{
					$output .= $event->getProgramData($user_guid);
				}			

				if($user_guid == get_loggedin_userid())
				{
					$output .= '<br /><a style="margin-left: 10px;" href="'.EVENT_MANAGER_BASEURL.'/event/register/'.$event->getGUID().'/event_attending">'.elgg_echo('event_manager:registration:edityourregistration').'</a>';
				}	

				$page_data = $title . $output;

				$body = elgg_view_layout("two_column_left_sidebar", "", $page_data);

				page_draw($title_text, $body);
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