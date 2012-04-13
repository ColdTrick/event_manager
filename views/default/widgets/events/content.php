<?php
 
	// TODO: is this needed here?
	elgg_load_js("event_manager.maps.base");
	elgg_load_js("event_manager.maps.helper");

	$widget = $vars["entity"];
	
	$owner = $widget->getOwnerEntity();
	
	elgg_push_context('widget');

	$event_options = array();
	$page_owner = elgg_get_page_owner_entity();
	if(elgg_in_context("groups") && $page_owner && ($page_owner instanceof ElggGroup)){		
		$event_options["container_guid"] = $page_owner->getGUID();
	} elseif(elgg_in_context("profile") || elgg_in_context("dashboard")) {
		if($vars['entity']->type_to_show == 'i created') {
			$event_options["owning"] = true;
		} elseif($vars['entity']->type_to_show == 'i\'m attending to') {
			$event_options["meattending"] = true;
		}
	}
	
	$event_options["limit"] = $vars['entity']->num_display;
	
	$events = event_manager_search_events($event_options);
	
	$limit = $widget->num_display;
	if(empty($limit)){
		$limit = 5;
	}
	
	$list_options = array(
			"offset" => 0,
			"limit" => $limit,
			"full_view" => false
		);
	
	$content = elgg_view_entity_list($events['entities'], $list_options);	
	
	elgg_pop_context();
	
	echo $content;