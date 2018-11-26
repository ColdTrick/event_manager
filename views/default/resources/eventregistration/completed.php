<?php

$event_guid = (int) elgg_extract('event_guid', $vars);
$object_guid = (int) elgg_extract('object_guid', $vars); // user or registration object

elgg_entity_gatekeeper($event_guid, 'object', Event::SUBTYPE);
$event = get_entity($event_guid);

elgg_entity_gatekeeper($object_guid);
$object = get_entity($object_guid);

if (!($object instanceof \ElggUser) && !($object instanceof \EventRegistration)) {
	forward('events');
}

// set page owner
elgg_set_page_owner_guid($event->getContainerGUID());

elgg_push_entity_breadcrumbs($event);

// build page elements
$title_text = elgg_echo('event_manager:registration:completed:title', [$event->getDisplayName()]);

$body = elgg_view('event_manager/registration/completed', [
	'event' => $event,
	'object' => $object,
]);

// build page
$page_data = elgg_view_layout('content', [
	'title' => $title_text,
	'content' => $body,
	'filter' => '',
]);

// draw page
echo elgg_view_page($title_text, $page_data);
