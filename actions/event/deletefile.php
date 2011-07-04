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
		}
	}
	
	if($event->canEdit())
	{
		$files = json_decode($event->files);
		$i = 0;
		foreach($files as $file)
		{
			if(strtolower($file->file) == strtolower($filename))
			{
				header('Content-Type: '.$file->mime);
				header('Content-Disposition: Attachment; filename='.$file->file);
				header('Pragma: no-cache');
				
				$prefix = "events/".$event->getGUID()."/files/";
				
				$fileHandler = new ElggFile();
				$fileHandler->setFilename($prefix . $file->file);
				if($fileHandler->owner_guid == $event->owner_guid)
				{
					$fileHandler->delete();
					unset($files[$i]);
				}
			}
			$i++;
		}
		$event->files = json_encode($files);
	}
	forward(REFERER);
}
	
register_error(elgg_echo('event_manager:event:file:notfound:text'));
forward(REFERER);