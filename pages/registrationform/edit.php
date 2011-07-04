<?php 
	gatekeeper();

	$title_text = elgg_echo("event_manager:editregistration:title");
	
	$guid = get_input("guid");
	
	if($entity = get_entity($guid))
	{	
		if($entity->getSubtype() == Event::SUBTYPE)
		{
			$event = $entity;
		}
	}
	
	if(!empty($event))
	{
		if($event->canEdit())
		{
			$back_text = '<div class="event_manager_back"><a href="'.$event->getURL().'">'.elgg_echo('event_manager:title:backtoevent').'</a></div>';
			
			$title = elgg_view_title($title_text . $back_text);
			
			$output  ='<ul id="event_manager_registrationform_fields">';
			
			if($registration_form = $event->getRegistrationFormQuestions())
			{
				foreach($registration_form as $question)
				{
					$output .= elgg_view('event_manager/registration/question', array('entity' => $question));
				}
			}
			
			$output .= '</ul>';	
			$output .= '<br /><a rel="'.$guid.'" id="event_manager_questions_add" href="javascript:void(0);">'.elgg_echo('event_manager:editregistration:addfield').'</a>&nbsp;';

			$content = elgg_view('page_elements/contentwrapper', array('body' => $output));
			
			$page_data = $title . $content;
			
			$body = elgg_view_layout("two_column_left_sidebar", "", $page_data);
			
			page_draw($title_text, $body);
		}
		else
		{
			forward($event->getURL());
		}
	}
	else
	{
		register_error(elgg_echo("geen guid"));
		forward(REFERER);
	}