<?php

	if(event_manager_has_maps_key())
	{
		$result = elgg_view('event_manager/event_sort_menu');
	}
	
	// show listing
	$result .= elgg_view("event_manager/list", $vars);
	
	if(event_manager_has_maps_key())
	{
		$result .= elgg_view("event_manager/onthemap", $vars);
	}
	
	if($vars["count"] > EVENT_MANAGER_SEARCH_LIST_LIMIT)
	{
		$result .= '<div id="event_manager_event_list_search_more" rel="'.((isset($vars["offset"]))?$vars["offset"]:EVENT_MANAGER_SEARCH_LIST_LIMIT).'">'.
			elgg_echo('event_manager:list:showmorevents').
				' ('.($vars["count"]-($offset+EVENT_MANAGER_SEARCH_LIST_LIMIT)).')</div>';
	}
	
	echo elgg_view('page_elements/contentwrapper', array('body' => $result));