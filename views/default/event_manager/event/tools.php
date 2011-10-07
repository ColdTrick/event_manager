<?php 
	$event = $vars["entity"];

	$toolLinks = '<span class="event_manager_event_actions_drop_down">'.elgg_echo('tools').'<ul>';
	$toolLinks .= '<li><a href="'.EVENT_MANAGER_BASEURL.'/event/edit/'.$event->getGUID().'">'.elgg_echo("event_manager:event:editevent").'</a></li>';
	$toolLinks .= '<li>'.elgg_view("output/confirmlink", array("href" => $vars["url"] . "action/event_manager/event/delete?guid=" . $event->getGUID(), "text" => elgg_echo("event_manager:event:deleteevent"))).'</li>';
	$toolLinks .= '<li><a href="'.EVENT_MANAGER_BASEURL.'/event/upload/'.$event->getGUID().'">'.elgg_echo("event_manager:event:uploadfiles").'</a></li>';
	if($event->registration_needed)
	{
		$toolLinks .= '<li><a href="'.EVENT_MANAGER_BASEURL.'/registrationform/edit/'.$event->getGUID().'">'.elgg_echo("event_manager:event:editquestions").'</a></li>';
		//$toolLinks .= '<li><a href="'.EVENT_MANAGER_BASEURL.'/registration/list/'.$event->getGUID().'">'.elgg_echo("event_manager:event:viewregistrations").'</a></li>';
	}
	/*if(isadminloggedin())
	{
		$sitetakeover = event_manager_check_sitetakeover_event();
		if($sitetakeover['count'] > 0)
		{
			if($sitetakeover['entities'][0]->getGUID() == $event->getGUID())
			{
				$t = 0;
				$link_text = elgg_echo("event_manager:event:disablesitetakeover");
			}
		}
		else
		{
			$t = 1;
			$link_text = elgg_echo("event_manager:event:setsitetakeover");
					
			if($event->access_id != 2)
			{
				$confirm = ' onclick="Javascript:if(!confirm(\''.addslashes(elgg_echo('event_manager:confirm:sitetakeover:access')).'\')){ return false; }" ';
			}
		}
		
		$toolLinks .= '<li><a '.$confirm.' href="'.elgg_add_action_tokens_to_url($vars["url"] . 'action/event_manager/event/sitetakeover?guid='.$event->getGUID()).'&t='.$t.'">'. $link_text . '</a></li>';
	}*/
	$toolLinks .= '<li><a href="'.elgg_add_action_tokens_to_url($vars["url"] . 'action/event_manager/attendees/export?guid='.$event->getGUID()).'">'.elgg_echo("event_manager:event:exportattendees").'</a></li>';
	$toolLinks .= '</ul></span>';
	
	echo $toolLinks;