<?php
/**
 * Event registration user icon
 *
 * Rounded avatar corners - CSS3 method
 * uses avatar as background image so we can clip it with border-radius in supported browsers
 *
 * @uses $vars['entity']     The user entity. If none specified, the current user is assumed.
 * @uses $vars['size']       The size - tiny, small, medium or large. (medium)
 * @uses $vars['class']      Optional class added to the .elgg-avatar div
 * @uses $vars['img_class']  Optional CSS class added to img
 * @uses $vars['link_class'] Optional CSS class for the link
 * @uses $vars['href']       Optional override of the link href
 */

$entity = elgg_extract('entity', $vars);
$size = elgg_extract('size', $vars, 'medium');

if (!$entity instanceof EventRegistration) {
	return;
}

$icon_sizes = elgg_get_icon_sizes('user');
if (!array_key_exists($size, $icon_sizes)) {
	$size = 'medium';
}

$event = $entity->getOwnerEntity();

$name = htmlspecialchars($entity->getDisplayName(), ENT_QUOTES, 'UTF-8', false);
$username = $entity->username;

$wrapper_class = [
	'elgg-avatar',
	"elgg-avatar-$size",
];
$wrapper_class = elgg_extract_class($vars, $wrapper_class);

$icon = elgg_view('output/img', [
	'src' => $entity->getIconURL($size),
	'alt' => $name,
	'title' => $name,
	'class' => elgg_extract_class($vars, [], 'img_class'),
]);

$content = elgg_format_element('a', [], $icon);

echo elgg_format_element('div', ['class' => $wrapper_class], $content);


//

// $result = '';
	
// if ($event instanceof Event && $event->canEdit()) {
// 	$result .= elgg_view_icon("hover-menu", "hidden");
// 	$result .= "<ul class='elgg-menu elgg-menu-hover'>";
// 	$result .= "<li><a href='javascript:void(0);'><span class='elgg-heading-basic'>{$entity->getDisplayName()}</span></a></li>";
// 	$result .= "<li><ul class='elgg-menu elgg-menu-hover-actions'>";
	
// 	$result .= '<li>';
// 	$result .= elgg_view('output/url', [
// 		'href' => elgg_generate_action_url('event_manager/event/rsvp', [
// 			'guid' => $event->guid,
// 			'user' => $entity->guid,
// 			'type' => EVENT_MANAGER_RELATION_UNDO,
// 		]),
// 		'text' => elgg_echo('event_manager:event:relationship:kick'),
// 	]);
// 	$result .= '</li>';
	
// 	$user_relationship = $event->getRelationshipByUser($entity->guid);
// 	if ($user_relationship == EVENT_MANAGER_RELATION_ATTENDING_PENDING) {
// 		$result .= '<li>';
// 		$result .= elgg_view('output/url', [
// 			'href' => elgg_generate_action_url('event_manager/event/resend_confirmation', [
// 				'guid' => $event->guid,
// 				'user' => $entity->guid,
// 			]),
// 			'text' => elgg_echo('event_manager:event:menu:user_hover:resend_confirmation'),
// 		]);
// 		$result .= '</li>';
		
// 		$result .= '<li>';
// 		$result .= elgg_view('output/url', [
// 			'href' => elgg_generate_action_url('event_manager/attendees/move_to_attendees', [
// 				'guid' => $event->guid,
// 				'user' => $entity->guid,
// 			]),
// 			'text' => elgg_echo('event_manager:event:menu:user_hover:move_to_attendees'),
// 		]);
// 		$result .= '</li>';
// 	}
			
// 	$result .= "</ul></li></ul>";
// }
