<?php 

$event = $vars["entity"];

$toolLinks = "<span class='event_manager_event_actions'>" . elgg_echo('tools') . "</span>";
$toolLinks .= "<ul class='event_manager_event_actions_drop_down'>";
$toolLinks .= "<li>" . elgg_view("output/url", array("href" => "events/event/upload/" . $event->getGUID(), "text" => elgg_echo("event_manager:event:uploadfiles"))) . "</li>";
if ($event->registration_needed) {
	$toolLinks .= "<li>" . elgg_view("output/url", array("href" => "events/registrationform/edit/" . $event->getGUID(), "text" => elgg_echo("event_manager:event:editquestions"))) . "</li>";
}

$toolLinks .= "</ul>";

echo $toolLinks;
