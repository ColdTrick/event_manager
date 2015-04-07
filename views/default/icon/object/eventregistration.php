<?php

$event = $vars["event"];
$entity = $vars["entity"];

if (!empty($event) && !empty($entity)) {
	$result = "<div class='elgg-avatar elgg-avatar-" . $vars["size"] . "'>";
		
	if ($event->canEdit()) {
		$result .= "<span class='elgg-icon elgg-icon-hover-menu hidden'></span>";
		$result .= "<ul class='elgg-menu elgg-menu-hover'><h3>" . $entity->name . "</h3><li>";
		$result .= "<ul class='elgg-menu elgg-menu-hover-actions'>";
		
		$result .= "<li>";
		$result .= elgg_view("output/url", array("href" => "action/event_manager/event/rsvp?guid=" . $event->getGUID() . "&user=" . $entity->getGUID() . "&type=" . EVENT_MANAGER_RELATION_UNDO, "text" => elgg_echo('event_manager:event:relationship:kick'), "is_action" => true));
		$result .= "</li>";
		
		$user_relationship = $event->getRelationshipByUser($entity->getGUID());
		if ($user_relationship == EVENT_MANAGER_RELATION_ATTENDING_PENDING) {
			$result .= "<li>";
			$result .= elgg_view("output/url", array("href" => 'action/event_manager/event/resend_confirmation?guid=' . $event->getGUID() . '&user=' . $entity->getGUID(), "text" => elgg_echo('event_manager:event:menu:user_hover:resend_confirmation'), "is_action" => true));
			$result .= "</li>";
			
			$result .= "<li>";
			$result .= elgg_view("output/url", array("href" => 'action/event_manager/attendees/move_to_attendees?guid=' . $event->getGUID() . '&user=' . $entity->getGUID(), "text" => elgg_echo('event_manager:event:menu:user_hover:move_to_attendees'), "is_action" => true));
			$result .= "</li>";
		}
				
		$result .= "</ul></li></ul>";
	}
	
	$result .= "<a>";
	$result .= "<img style='background: url(" . elgg_get_site_url() . "_graphics/icons/user/default" . $vars["size"] . ".gif) no-repeat scroll 0% 0% transparent;'";
	$result .= " src='" . elgg_get_site_url() . "_graphics/spacer.gif'";
	$result .= " alt='" . $entity->name . "'";
	$result .= " title='" . $entity->name . "' />";
	$result .= "</a>";
	$result .= "</div>";
	
	echo $result;
}