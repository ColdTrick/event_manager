<?php

	global $CONFIG;
	
	$event = $vars["entity"];
	
	if($files = $event->hasFiles())
	{
		$user_path = 'events/'.$event->getGUID().'/files/';
		
		echo '<ul class="event_manager_event_files">';
		foreach($files as $file)
		{
			echo '<li><a href="'.EVENT_MANAGER_BASEURL.'/event/file/'.$event->getGUID().'/'.$file->file.'">'.$file->title.'</a></li>';
		}
		echo '</ul>';
	}