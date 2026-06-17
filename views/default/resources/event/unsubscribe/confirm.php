<?php

use Elgg\Exceptions\Http\BadRequestException;

$guid = (int) elgg_extract('guid', $vars);
$code = elgg_extract('code', $vars);

$registration = elgg_entity_gatekeeper($guid, 'object', \EventRegistration::SUBTYPE);

$event = $registration->getOwnerEntity();
$verify_code = event_manager_create_unsubscribe_code($registration, $event);

if (empty($code) || ($code !== $verify_code)) {
	$exception = new BadRequestException(elgg_echo('event_manager:unsubscribe_confirm:error:code'));
	$exception->setRedirectUrl(REFERRER);
	throw $exception;
}

elgg_set_page_owner_guid($event->getContainerGUID());

elgg_push_entity_breadcrumbs($event);

echo elgg_view_page(elgg_echo('event_manager:unsubscribe_confirm:title', [$event->getDisplayName()]), [
	'content' => elgg_view_form('event_manager/event/unsubscribe_confirm', [], [
		'entity' => $event,
		'registration' => $registration,
		'code' => $code,
	]),
	'filter' => false,
]);
