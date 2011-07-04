<?php

	$guid = get_input("guid");
	
	if($entity = get_entity($guid))
	{	
		if($entity->getSubtype() == Event::SUBTYPE)
		{
			$event = $entity;
		}
	}
	
	if($event && $event->canEdit())
	{
		
		$title_text = elgg_echo('Export attendees');
		
		$download_link = '<h3 class="settings">'.elgg_echo('File download').'</h3>';
		$download_link .= '<a href="'.elgg_add_action_tokens_to_url($vars['url'].'/action/event_manager/attendees/export?type=tofile&guid='.$guid).'">Download export file</a><br />';
		
		$form .= elgg_view("event_manager/forms/attendees/export", array('guid' => $guid));
		
		$body = elgg_view('page_elements/contentwrapper', array('body' => $download_link.$form));	
		$title = elgg_view_title($title_text . $back_text);
		
		$page_data = $title . $body;
						
		$content = elgg_view_layout("two_column_left_sidebar", "", $page_data);
		
		page_draw($title_text, $content);
	} 
	else 
	{
		register_error("no guid");
		forward(REFERER);
	}