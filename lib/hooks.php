<?php 
	
	function event_manager_eventicon_hook($hook, $entity_type, $returnvalue, $params)
	{
		global $CONFIG;

		if ((!$returnvalue) && ($hook == 'entity:icon:url') && ($params['entity'] instanceof Event))
		{
			$entity 	= $params['entity'];
			$type 		= $entity->type;
			$subtype 	= get_subtype_from_id($entity->subtype);
			$viewtype 	= $params['viewtype'];
			$size 		= $params['size'];
			$title 		= $entity->title;

			if ($icontime = $entity->icontime) 
			{
				$icontime = "{$icontime}";
			} 
			else 
			{
				$icontime = "default";
			}

			$filehandler = new ElggFile();
			$filehandler->owner_guid = $entity->getOwner();
			$filehandler->setFilename("events/".$entity->guid."/".$size.".jpg");

			if ($filehandler->exists())
			{
				return $CONFIG->wwwroot.'mod/event_manager/icondirect.php?lastcache='.$icontime.'&joindate='.$entity->time_created.'&guid='.$entity->guid.'&size='.$size;
			}
		}
	}
	
	function event_manager_register_postdata_hook($hook, $entity_type, $returnvalue, $params)
	{
		$_SESSION['registerevent_values'] = $_POST;
	}