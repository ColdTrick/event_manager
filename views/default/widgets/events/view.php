<?php

	$widget = $vars["entity"];
	
	$owner = $widget->getOwnerEntity();
	
	$current_context = get_context();
	set_context('widget');

	$event_options = array();
	$page_owner = page_owner_entity();
	if(($current_context == 'groups') && $page_owner && ($page_owner instanceof ElggGroup))
	{		
		$event_options["container_guid"] = $page_owner->getGUID();
	}
	elseif(in_array($current_context, array('profile', 'dashboard')))
	{
		if($vars['entity']->type_to_show == 'i created')
		{
			$event_options["owning"] = true;
		}
		elseif($vars['entity']->type_to_show == 'i\'m attending to')
		{
			$event_options["meattending"] = true;
		}
	}
	
	$event_options["limit"] = $vars['entity']->num_display;
	
	$events = event_manager_search_events($event_options);
	$content = elgg_view_entity_list($events['entities'], 0, 0, 5, false);	
	
	set_context($current_context);

	echo $content;