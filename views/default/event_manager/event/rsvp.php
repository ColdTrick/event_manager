<?php 

	$event = $vars["entity"];
	$event_relationship_options = event_manager_event_get_relationship_options();
	
	if(isloggedin() && (get_loggedin_userid() != $event->owner_guid))
	{
		$user_relation = $event->getRelationshipByUser();
			
		echo "<span class='event_manager_event_actions_drop_down event_manager_event_select_relationship'>";
		if($user_relation) 
		{
			echo "<span class='event_manager_event_select_relationship_selected'>" . elgg_echo("event_manager:event:rsvp") . "</span>";
		} 
		else 
		{
			echo elgg_echo("event_manager:event:rsvp");
		}
		
		echo "<ul>";
		foreach($event_relationship_options as $rel)
		{
			if(($rel == EVENT_MANAGER_RELATION_ATTENDING) || $event->$rel)
			{
				if($rel == EVENT_MANAGER_RELATION_ATTENDING)
				{
					if(!$event->hasEventSpotsLeft() && !$event->waiting_list_enabled)
					{
						continue;
					}
				}
				
				if($rel == $user_relation)
				{
					echo "<li class='selected'>" . elgg_echo('event_manager:event:relationship:' . $rel) . "</li>";
				} 
				else 
				{
					if($rel != EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST)
					{
						$action_url = elgg_add_action_tokens_to_url($vars["url"] . "action/event_manager/event/rsvp?guid=" . $event->getGUID() . "&type=" . $rel);
						echo "<li><a href='" . $action_url . "'>" . elgg_echo('event_manager:event:relationship:' . $rel) . "</a></li>";
					}
				}
			}
		}
		
		if($user_relation)
		{
			$action_url = elgg_add_action_tokens_to_url($vars["url"] . "action/event_manager/event/rsvp?guid=" . $event->getGUID() . "&type=" . EVENT_MANAGER_RELATION_UNDO);
			echo "<li><a href='" . $action_url . "'>" . elgg_echo('event_manager:event:relationship:undo') . "</a></li>";
		}
		echo "</ul>";
		echo "</span>";
	}