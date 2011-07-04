<?php

	$event = $vars['entity'];
	$user_relation = $event->getRelationshipByUser();
	
	if($user_relation && ($user_relation == EVENT_MANAGER_RELATION_ATTENDING) && $event->registration_needed && ($user_guid = get_loggedin_userid()))
	{
		if($event->isAttending())
		{
			echo '<a href="' . EVENT_MANAGER_BASEURL . '/registration/view/' . $event->getGUID() . '">' . elgg_echo("event_manager:registration:viewyourregistration") . '</a>';
		}	
	}