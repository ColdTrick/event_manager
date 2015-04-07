<?php

	$event = elgg_extract("entity", $vars);
	$registration = elgg_extract("registration", $vars);
	$code = elgg_extract("code", $vars);
	
	echo elgg_view("output/longtext", array("value" => elgg_echo("event_manager:unsubscribe_confirm:description", array($registration->name, $event->title))));
	
	echo "<div class='elgg-foot'>";
	echo elgg_view("input/hidden", array("name" => "registration", "value" => $registration->getGUID()));
	echo elgg_view("input/hidden", array("name" => "event", "value" => $event->getGUID()));
	echo elgg_view("input/hidden", array("name" => "code", "value" => $code));
	echo elgg_view("input/submit", array("value" => elgg_echo("confirm")));
	echo "</div>";