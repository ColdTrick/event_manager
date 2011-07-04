<?php 
	gatekeeper();

	$guid = get_input('guid');
	$filter = get_input("filter", "waiting");
	
	if($entity = get_entity($guid))
	{	
		if($entity->getSubtype() == Event::SUBTYPE)
		{
			$event = $entity;
		}
	}
	
	if(!empty($event))
	{
		if(!$event->canEdit())
		{
			forward($event->getURL());
		}
		else
		{
			$registrations = $event->getAllRegistrations($filter);
			
			$list = elgg_view_entity_list($registrations['entities'], 999, 0, false, false);		

			$title_text = elgg_echo("event_manager:event:viewregistrations");
			$back_text = '<div class="event_manager_back"><a href="'.$event->getURL().'">'.elgg_echo('event_manager:title:backtoevent').'</a></div>';
			$title = elgg_view_title($title_text . $back_text);
			
			$navigation = elgg_view('event_manager/registration_sort_menu', array('eventguid' => $guid, 'filter' => $filter));
			
			$page_data = $title . $list;
						
			$body = elgg_view_layout("two_column_left_sidebar", "", $page_data);
			
			page_draw($title_text, $body);
		}
	}
	else
	{	
		register_error(elgg_echo("event_manager:event_not_found"));
		forward(REFERER);
	}