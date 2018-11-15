<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof EventRegistration) {
	return;
}

$event = $entity->getOwnerEntity();

$size = elgg_extract('size', $vars);
$class = elgg_extract_class($vars, [
	'elgg-avatar',
	"elgg-avatar-{$size}",
]);

$result = '';
	
if ($event instanceof Event && $event->canEdit()) {
	$result .= elgg_view_icon("hover-menu", "hidden");
	$result .= "<ul class='elgg-menu elgg-menu-hover'>";
	$result .= "<li><a href='javascript:void(0);'><span class='elgg-heading-basic'>{$entity->getDisplayName()}</span></a></li>";
	$result .= "<li><ul class='elgg-menu elgg-menu-hover-actions'>";
	
	$result .= '<li>';
	$result .= elgg_view('output/url', [
		'href' => elgg_http_add_url_query_elements('action/event_manager/event/rsvp', [
			'guid' => $event->guid,
			'user' => $entity->guid,
			'type' => EVENT_MANAGER_RELATION_UNDO,
		]),
		'text' => elgg_echo('event_manager:event:relationship:kick'),
		'is_action' => true
	]);
	$result .= '</li>';
	
	$user_relationship = $event->getRelationshipByUser($entity->guid);
	if ($user_relationship == EVENT_MANAGER_RELATION_ATTENDING_PENDING) {
		$result .= '<li>';
		$result .= elgg_view('output/url', [
			'href' => elgg_http_add_url_query_elements('action/event_manager/event/resend_confirmation', [
				'guid' => $event->guid,
				'user' => $entity->guid,
			]),
			'text' => elgg_echo('event_manager:event:menu:user_hover:resend_confirmation'),
			'is_action' => true,
		]);
		$result .= '</li>';
		
		$result .= '<li>';
		$result .= elgg_view('output/url', [
			'href' => elgg_http_add_url_query_elements('action/event_manager/attendees/move_to_attendees', [
				'guid' => $event->guid,
				'user' => $entity->guid,
			]),
			'text' => elgg_echo('event_manager:event:menu:user_hover:move_to_attendees'),
			'is_action' => true,
		]);
		$result .= '</li>';
	}
			
	$result .= "</ul></li></ul>";
}

$result .= elgg_view('output/url', [
	'text' => elgg_view('output/img', [
		'src' => elgg_get_simplecache_url("icons/user/default{$size}.gif"),
		'alt' => $entity->getDisplayName(),
		'title' => $entity->getDisplayName(),
	]),
]);

echo elgg_format_element('div', ['class' => $class], $result);
