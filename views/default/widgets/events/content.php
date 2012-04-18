<?php

	$widget = $vars["entity"];
	
	$owner = $widget->getOwnerEntity();
	
	$event_options = array();
	
	switch($owner->getType()){
		case "group":
			$event_options["container_guid"] = $owner->getGUID();
			break;
		case "user":
			switch($widget->type_to_show){
				case "owning":
					$event_options["owning"] = true;
					break;
				case "attending":
					$event_options["meattending"] = true;
					break;
			}
			break;
	}
	
	$num_display = (int) $widget->num_display;
	if($num_display < 1){
		$num_display = 5;
	}
	$event_options["limit"] = $num_display;
	
	$events = event_manager_search_events($event_options);
	$content = elgg_view_entity_list($events['entities'], $events["count"], 0, $num_display, false, false, false);	
	
	echo $content;