<?php 

	$title_text = elgg_echo("event_manager:list:title");
	$title = elgg_view_title($title_text);
	
	$event_options = array();
	
	if(($page_owner = page_owner_entity()) && ($page_owner instanceof ElggGroup))
	{
		$event_options["container_guid"] = $page_owner->getGUID();
	}
	
	$events = event_manager_search_events($event_options);
	
	$entities = $events["entities"];
	$count = $events["count"];
	
	$form = elgg_view("event_manager/forms/event/search");
	
	$result = elgg_view("event_manager/search_result", array("entities" => $entities, "count" => $count));
	
	$content = 	$form . $result;
	
	$page_data = $title . $content;
	
	$body = elgg_view_layout("two_column_left_sidebar", "", $page_data);
	
	page_draw($title_text, $body);