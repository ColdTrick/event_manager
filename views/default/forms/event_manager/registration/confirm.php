<?php

	$event = elgg_extract("event", $vars);
	$user = elgg_extract("user", $vars);
	$code = elgg_extract("code", $vars);
	
	echo elgg_view("output/longtext", array("value" => elgg_echo("event_manager:registration:confirm:description", array($user->name, $event->title))));
	
	echo "<div class='elgg-foot'>";
	echo elgg_view("input/hidden", array("name" => "event_guid", "value" => $event->getGUID()));
	echo elgg_view("input/hidden", array("name" => "user_guid", "value" => $user->getGUID()));
	echo elgg_view("input/hidden", array("name" => "code", "value" => $code));
	echo elgg_view("input/submit", array("value" => elgg_echo("confirm")));
	echo "</div>";