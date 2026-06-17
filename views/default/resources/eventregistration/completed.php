<?php

use Elgg\Exceptions\Http\BadRequestException;

$event_guid = (int) elgg_extract('event_guid', $vars);
$object_guid = (int) elgg_extract('object_guid', $vars); // user or registration object

/* @var $event \Event */
$event = elgg_entity_gatekeeper($event_guid, 'object', \Event::SUBTYPE);

$object = elgg_entity_gatekeeper($object_guid);
if (!$object instanceof \ElggUser && !$object instanceof \EventRegistration) {
	$exception = new BadRequestException();
	$exception->setRedirectUrl(elgg_generate_url('collection:object:event:upcoming'));
	throw $exception;
}

elgg_set_page_owner_guid($event->getContainerGUID());

elgg_push_entity_breadcrumbs($event);

echo elgg_view_page(elgg_echo('event_manager:registration:completed:title', [$event->getDisplayName()]), [
	'content' => elgg_view('event_manager/registration/completed', [
		'event' => $event,
		'object' => $object,
	]),
	'filter' => false,
]);
