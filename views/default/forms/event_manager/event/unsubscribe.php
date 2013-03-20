<?php

	$entity = elgg_extract("entity", $vars);

	echo elgg_view("output/longtext", array("value" => elgg_echo("event_manager:unsubscribe:description", array($entity->title))));
	
	echo "<div>";
	echo "<label for='event-manager-unsubscribe-email'>" . elgg_echo("email") . "</label>";
	echo elgg_view("input/email", array("name" => "email", "value" => elgg_get_sticky_value("event_unsubscribe", "email"), "id" => "event-manager-unsubscribe-email"));
	echo "</div>";
	
	echo "<div class='elgg-foot'>";
	echo elgg_view("input/hidden", array("name" => "guid", "value" => $entity->getGUID()));
	echo elgg_view("input/submit", array("value" => elgg_echo("submit")));
	echo "</div>";
	
	// cleanup sticky form dta
	elgg_clear_sticky_form("event_unsubscribe");