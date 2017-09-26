<?php

$event = elgg_extract('event', $vars);
$entity = elgg_extract('entity', $vars);

if (empty($event) || empty($entity)) {
	return;
}

$size = elgg_extract('size', $vars);

$result = "<div class='elgg-avatar elgg-avatar-{$size}'>";
	
if ($event->canEdit()) {
	$result .= elgg_view_icon("hover-menu", "hidden");
	$result .= "<ul class='elgg-menu elgg-menu-hover'>";
	$result .= "<li><a href='javascript:void(0);'><span class='elgg-heading-basic'>" . $entity->name . "</span></a></li>";
	$result .= "<li><ul class='elgg-menu elgg-menu-hover-actions'>";
	
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

$result .= elgg_view('output/url', [
	'text' => elgg_view('output/img', [
		'src' => elgg_get_simplecache_url("icons/user/default{$size}.gif"),
		'alt' => $entity->name,
		'title' => $entity->name,
	]),
]);

$result .= "</div>";

echo $result;
