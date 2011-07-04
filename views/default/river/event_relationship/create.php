<?php

	$user = get_entity($vars['item']->subject_guid);
	$event = get_entity($vars['item']->object_guid);
	
	$subject_url = "<a href=\"{$user->getURL()}\">{$user->name}</a>";
	$event_url = "<a href=\"" . $event->getURL() . "\">" . $event->title . "</a>";
	
	$relationtype = $event->getRelationshipByUser($user->getGUID()); 
	
	$string = sprintf(elgg_echo("event_manager:river:event_relationship:create:" . $relationtype),$subject_url, $event_url);
	
	echo $string;
?>