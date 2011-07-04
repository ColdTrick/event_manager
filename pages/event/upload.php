<?php
	global $CONFIG; 
	
	gatekeeper();
	
	$guid = get_input("guid");

	$title_text = elgg_echo("event_manager:edit:upload:title");
	
	if(!empty($guid) && ($entity = get_entity($guid)))
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
	
		$form = elgg_view("event_manager/forms/event/upload", array("entity" => $event));
		
		$files = json_decode($event->files);
		
		if(count($files) > 0)
		{
			$user_path = 'events/'.$event->getGUID().'/files/';
			
			$currentFiles .= '<h3 class="settings">Uploaded files</h3>';
			$currentFiles .= '<ul class="event_manager_event_files">';
			foreach($files as $file)
			{
				$currentFiles .= '	<li><a href="'.EVENT_MANAGER_BASEURL.'/event/file/'.$event->getGUID().'/'.$file->file.'">'.$file->title.'</a>
										<a onclick="javascript: if(!confirm(\''.elgg_echo('question:areyousure').'\')){return false;}" href="'.elgg_add_action_tokens_to_url($CONFIG->wwwroot.'action/event_manager/event/deletefile?guid='.$event->getGUID().'&file='.$file->file).'">
											<span class="event_manager_event_file_delete"></span>
										</a>
									</li>';
			}
			$currentFiles .= '</ul>';
		}
		$back_text = '<div class="event_manager_back"><a href="'.$event->getURL().'">'.elgg_echo('event_manager:title:backtoevent').'</a></div>';
		$title = elgg_view_title($title_text . $back_text);

		$currentFiles = elgg_view("page_elements/contentwrapper", array("body" => $currentFiles));
		
		$page_data = $title . $form . $currentFiles;
		
		$body = elgg_view_layout("two_column_left_sidebar", "", $page_data);
		
		page_draw($title_text, $body);
	}
	else
	{
		register_error(elgg_echo('noguid'));
		forward(REFERER);
	}