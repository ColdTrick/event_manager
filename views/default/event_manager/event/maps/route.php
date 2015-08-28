<?php

echo "<div id='google_maps' style='width: 500px; height: 425px; overflow:hidden;'>";
echo "<div id='map_canvas' style='width: 500px; height: 300px;'></div>";

$form_body .= "<label>" . elgg_echo("from") . ": *</label>";
$form_body .= elgg_view("input/text", [
	"name" => "address_from", 
	"id" => "address_from"
]) . "<br />";
$form_body .= "<label>" . elgg_echo("to") . ": </label><br />";
$form_body .= "<span id='address_to'>" . get_input("from") . "</span><br />";

$form_body .= "<a class='hidden' target='_blank' href='' id='openRouteLink'>google maps</a>";
$form_body .= elgg_view("input/submit", [
	"name" => "address_route_search", 
	"id" => "address_route_search", 
	"type" => "button", 
	"value" => elgg_echo("calculate_route")
]) . "&nbsp";

echo elgg_view("input/form", [
		"id" => "event_manager_address_route_search",
		"name" => "event_manager_address_route_search",
		"body" => $form_body
]);

echo "</div>";
