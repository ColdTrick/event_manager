<?php 

	$guid = get_input('guid');
	$filename = get_input('file');
	$time = get_input('t');

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
			$files = json_decode($event->files, true);
			$i = 0;
			
			foreach($files as $file)
			{
				if((strtolower($file['file']) == strtolower($filename)) && ($time == $file['time_uploaded']))
				{
					$prefix = "events/".$event->getGUID()."/files/";

					$fileHandler = new ElggFile();
					$fileHandler->setFilename($prefix . $file['file']);
					$fileHandler->owner_guid = $event->owner_guid;
					
					$fileHandler->delete();
					
					if(count($files) == 1)
					{
						$files = array();
					}
					else
					{
						unset($files[$i]);
					}
					break;
				}

				$i++;
			}
			
			$event->files = json_encode($files);
		}
		
		forward(REFERER);
	}
	
	register_error(elgg_echo('event_manager:event:file:notfound:text'));
	forward(REFERER);