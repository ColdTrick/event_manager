<?php

$guid = (int) elgg_extract('guid', $vars);

elgg_entity_gatekeeper($guid, 'object', \Event::SUBTYPE);
$event = get_entity($guid);

elgg_set_page_owner_guid($event->getContainerGUID());

if (!$event->waiting_list_enabled) {
	forward($event->getURL());
}

if (!$event->openForRegistration()) {
	register_error(elgg_echo('event_manager:event:rsvp:registration_ended'));
	forward($event->getURL());
}

$title_text = elgg_echo('event_manager:event:rsvp:waiting_list');

elgg_push_entity_breadcrumbs($event);

$form_vars = [
	'id' => 'event_manager_event_register',
	'name' => 'event_manager_event_register',
];
$body_vars = [
	'entity' => $event,
	'register_type' => 'waitinglist',
];

$form = elgg_view_form('event_manager/event/register', $form_vars, $body_vars);

$body = elgg_view_layout('default', [
	'filter' => false,
	'content' => $form,
	'title' => $title_text,
]);

echo elgg_view_page($title_text, $body);
