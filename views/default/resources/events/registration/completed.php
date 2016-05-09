<?php

$event_guid = (int) elgg_extract('event_guid', $vars);
$object_guid = (int) elgg_extract('object_guid', $vars);

if (empty($event_guid) || empty($object_guid)) {
	register_error(elgg_echo('InvalidParameterException:NoEntityFound'));
	forward('events');
}

$event = get_entity($event_guid);
$object = get_entity($object_guid);

if (!($event instanceof Event)) {
	register_error(elgg_echo('ClassException:ClassnameNotClass', [$event_guid, elgg_echo('item:object:' . Event::SUBTYPE)]));
	forward('events');
}

if (!($object instanceof ElggUser) && !($object instanceof EventRegistration)) {
	forward('events');
}

// set page owner
elgg_set_page_owner_guid($event->getContainerGUID());

// set breadcrumb
elgg_push_breadcrumb($event->title, $event->getURL());

// build page elements
$title_text = elgg_echo('event_manager:registration:completed:title', [$event->title]);

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
