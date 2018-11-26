<?php

$event_guid = (int) elgg_extract('guid', $vars);
$user_guid = (int) elgg_extract('user_guid', $vars, get_input('user_guid'));
$code = elgg_extract('code', $vars, get_input('code'));

elgg_entity_gatekeeper($event_guid, 'object', Event::SUBTYPE);
$event = get_entity($event_guid);

elgg_entity_gatekeeper($user_guid);
$user = get_entity($user_guid);

// is the code valid
if (!event_manager_validate_registration_validation_code($event_guid, $user_guid, $code)) {
	throw new \Elgg\HttpException(elgg_echo('event_manager:registration:confirm:error:code'), ELGG_HTTP_FORBIDDEN);
}

// do we have a pending registration
if ($event->getRelationshipByUser($user_guid) !== EVENT_MANAGER_RELATION_ATTENDING_PENDING) {
	forward($event->getURL());
}

// set page owner
elgg_set_page_owner_guid($event->getContainerGUID());

// build breadcrumb
elgg_push_breadcrumb($event->getDisplayName(), $event->getURL());

// let's show the confirm form
$title_text = elgg_echo('event_manager:registration:confirm:title', [$event->getDisplayName()]);

$body_vars = [
	'event' => $event,
	'user' => $user,
	'code' => $code,
];
$form = elgg_view_form('event_manager/registration/confirm', [], $body_vars);

// build page
$page_data = elgg_view_layout('content', [
	'title' => $title_text,
	'content' => $form,
	'filter' => ''
]);

// draw page
echo elgg_view_page($title_text, $page_data);
