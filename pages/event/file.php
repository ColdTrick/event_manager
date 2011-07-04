<?php 

$guid = get_input('guid');
$filename = get_input('file');

if(!empty($guid) && !empty($filename))
{
	if($entity = get_entity($guid))
	{
		if($entity->getSubtype() == Event::SUBTYPE)
		{
			$event = $entity;
			$back_text = '<div class="event_manager_back"><a href="'.$event->getURL().'">'.elgg_echo('event_manager:title:backtoevent').'</a></div>';
		}
	}
	
	$files = json_decode($event->files);
	foreach($files as $file)
	{
		if(strtolower($file->file) == strtolower($filename))
		{
			$prefix = "events/".$event->getGUID()."/files/";
				
			$fileHandler = new ElggFile();
			$fileHandler->setFilename($prefix . $file->file);
			
			$fileHandler->owner_guid = $event->owner_guid;
			
			header('Content-Type: '.$file->mime);
			header('Content-Disposition: Attachment; filename='.$file->file);
			header('Pragma: no-cache');				
			
			echo $fileHandler->grabFile();
			exit;
		}
	}
}




$title_text = elgg_echo('event_manager:event:file:notfound:title');
$title = elgg_view_title($title_text . $back_text);

$page_data = $title. elgg_echo('event_manager:event:file:notfound:text');

$body = elgg_view_layout("two_column_left_sidebar", "", $page_data);

page_draw($title_text, $body);