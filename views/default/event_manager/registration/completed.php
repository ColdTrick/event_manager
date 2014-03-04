<?php

	$event = elgg_extract("event", $vars);
	$object = elgg_extract("object", $vars);
	
	if(!($completed_text = $event->registration_completed)) {
		$completed_text = elgg_echo("event_manager:registration:completed", array($object->name, $event->title));
	}
	
	$completed_text = str_ireplace("[NAME]", $object->name, $completed_text);
	$completed_text = str_ireplace("[EVENT]", $event->title, $completed_text);
	
	echo elgg_view("output/longtext", array("value" => $completed_text));
	
	$rel = $event->getRelationshipByUser($object->getGUID());
	echo elgg_view("output/longtext", array("value" => elgg_echo("event_manager:event:relationship:message:" . $rel)));
	
	echo "<div class='mtm'>";
	echo elgg_view("output/url", array("text" => elgg_echo("event_manager:registration:continue"), "href" => $event->getURL(), "class" => "elgg-button elgg-button-action"));
	echo "</div>";