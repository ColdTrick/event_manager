<?php

	if(event_manager_has_maps_key())
	{
		$result = elgg_view('event_manager/event_sort_menu');
	}
	
	
	$list = elgg_view("event_manager/list", $vars);
	if(!empty($list))
	{
		$result .= $list;
	}
	else
	{
		$result .= elgg_echo('event_manager:list:noresults');
	}
	
	if(event_manager_has_maps_key())
	{
		$result .= elgg_view("event_manager/onthemap", $vars);
	}
	
	if($count > EVENT_MANAGER_SEARCH_LIST_LIMIT)
	{
		$result .= '<div id="event_manager_event_list_search_more" rel="'.(($offset)?$offset:EVENT_MANAGER_SEARCH_LIST_LIMIT).'">'.elgg_echo('event_manager:list:showmorevents').' ('.($count-($offset+EVENT_MANAGER_SEARCH_LIST_LIMIT)).')</div>';
	}
	echo elgg_view('page_elements/contentwrapper', array('body' => $result));

?>