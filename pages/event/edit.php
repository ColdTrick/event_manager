<?php 

	gatekeeper();

	$title_text = elgg_echo("event_manager:edit:title");
	
	$guid = get_input("guid");
	
	if(!empty($guid) && ($entity = get_entity($guid)))
	{	
		if($entity->getSubtype() == Event::SUBTYPE)
		{
			$event = $entity;
			$back_text = '<div class="event_manager_back"><a href="'.$event->getURL().'">'.elgg_echo('event_manager:title:backtoevent').'</a></div>';
			
			set_page_owner($event->container_guid);
		}
	}
	else 
	{
		$forward = true;
		$page_owner = page_owner_entity();
		
		if($page_owner && ($page_owner instanceof ElggGroup)){
			$who_create_group_events = get_plugin_setting('who_create_group_events', 'event_manager'); // group_admin, members
			
			if(!empty($who_create_group_events)){
				if((($who_create_group_events == "group_admin") && $page_owner->canEdit()) || (($who_create_group_events == "members") && $page_owner->isMember($user))){
					$forward = false;  	
				} 
			} 
			
		} else {
			$who_create_site_events = get_plugin_setting('who_create_site_events', 'event_manager');
			if(($who_create_site_events != 'admin_only') || isadminloggedin()){
				$forward = false;
			}
			set_page_owner(get_loggedin_userid());
		}
		if($forward){
			forward(EVENT_MANAGER_BASEURL);
		}
	}

	$form = elgg_view("event_manager/forms/event/edit", array("entity" => $event));

	$title = elgg_view_title($title_text . $back_text);
	
	$page_data = $title . $form;
	
	$body = elgg_view_layout("two_column_left_sidebar", "", $page_data);
	
	page_draw($title_text, $body);
	