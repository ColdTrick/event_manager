<?php 
admin_gatekeeper();

$guid = get_input("guid");
$t = get_input("t", 1);


if((!event_manager_check_sitetakeover_event() && ($t == 1)) || ($t == 0))
{
	if(!empty($guid) && ($entity = get_entity($guid)))
	{
		if($entity instanceof Event)
		{
			$event = $entity;
			
			if($t == 1)
			{
				$old_access = $event->access_id;
				$event->old_access = $old_access;
				
				$event->setAccessToOwningObjects(2);
			}
			else
			{
				$old_access = $event->old_access;
				$event->setAccessToOwningObjects($old_access);
			}
			
			$event->site_takeover = $t;
			$event->save();
		}
	}
}

forward(REFERER);