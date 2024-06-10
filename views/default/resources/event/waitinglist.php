<?php

use Elgg\Exceptions\HttpException;

$guid = (int) elgg_extract('guid', $vars);
elgg_entity_gatekeeper($guid, 'object', \Event::SUBTYPE);

/* @var $event \Event */
$event = get_entity($guid);

elgg_set_page_owner_guid($event->getContainerGUID());

if (!$event->waiting_list_enabled) {
	$exception = new HttpException();
	$exception->setRedirectUrl($event->getURL());
	throw $exception;
}

if (!$event->openForRegistration()) {
	$exception = new HttpException(elgg_echo('event_manager:event:rsvp:registration_ended'));
	$exception->setRedirectUrl($event->getURL());
	throw $exception;
}

elgg_push_entity_breadcrumbs($event);

echo elgg_view_page(elgg_echo('event_manager:event:rsvp:waiting_list'), [
	'content' => elgg_view_form('event_manager/event/register', [
		'id' => 'event_manager_event_register',
		'name' => 'event_manager_event_register',
	], [
		'entity' => $event,
		'register_type' => 'waitinglist',
	]),
	'filter' => false,
]);
