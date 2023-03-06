<?php

/**
 * @var \Event $event
 */
$event = elgg_extract('entity', $vars);
$result = '';

$title = elgg_view('event_manager/event/view/banner', $vars);
$title .= elgg_view_title($event->getDisplayName(), [
	'class' => 'elgg-heading-main',
]);

$result .= elgg_format_element('div', ['class' => 'event-manager-popup-title'], $title);

$datetime = elgg_view('event_manager/event/view/datetime', $vars);
$registration = elgg_view('event_manager/event/view/registration', $vars + ['show_rsvp' => false]);

$result .= elgg_format_element('div', ['class' => 'event-manager-header'], $datetime . $registration);

// description
$description = $event->description ? elgg_get_excerpt($event->description, 500) : $event->shortdescription;
if (!empty($description)) {
	$result .= elgg_view_module('event', '', elgg_view('output/longtext', ['value' => $description]));
}

$location = '';
if ($event->location) {
	$location_details = elgg_format_element('label', [], elgg_echo('event_manager:edit:form:location'));
	$location_details .= elgg_format_element('div', ['class' => 'mrl'], elgg_view_url($event->getURL() . '#location', $event->location));
	
	$location = elgg_format_element('div', [], $location_details);
}

$event_link = elgg_view('output/url', [
	'href' => $event->getURL(),
	'text' => elgg_echo('event_manager:popup:event_link'),
	'class' => ['elgg-button', 'elgg-button-action', 'mts'],
]);

$result .= elgg_format_element('div', ['class' => 'event-manager-popup-footer'], $location . $event_link);

$classes = ['event-manager-popup'];
if ($event->owner_guid === elgg_get_logged_in_user_guid()) {
	$classes[] = 'event-manager-event-owner';
} elseif ($event->getRelationshipByUser()) {
	$classes[] = 'event-manager-event-attending';
}

echo elgg_format_element('div', ['class' => $classes], $result);
