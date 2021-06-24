<?php

use Elgg\Exceptions\HttpException;

$event_guid = (int) elgg_extract('guid', $vars);
$user_guid = (int) elgg_extract('user_guid', $vars, get_input('user_guid'));
$code = elgg_extract('code', $vars, get_input('code'));

elgg_entity_gatekeeper($event_guid, 'object', Event::SUBTYPE);
$event = get_entity($event_guid);

elgg_entity_gatekeeper($user_guid);
$user = get_entity($user_guid);

// is the code valid
if (!event_manager_validate_registration_validation_code($event_guid, $user_guid, $code)) {
	throw new HttpException(elgg_echo('event_manager:registration:confirm:error:code'), ELGG_HTTP_FORBIDDEN);
}

// do we have a pending registration
if ($event->getRelationshipByUser($user_guid) !== EVENT_MANAGER_RELATION_ATTENDING_PENDING) {
	$exception = new HttpException();
	$exception->setRedirectUrl($event->getURL());
	throw $exception;
}

// set page owner
elgg_set_page_owner_guid($event->getContainerGUID());

elgg_push_entity_breadcrumbs($event);

$body_vars = [
	'event' => $event,
	'user' => $user,
	'code' => $code,
];
$form = elgg_view_form('event_manager/registration/confirm', [], $body_vars);

echo elgg_view_page(elgg_echo('event_manager:registration:confirm:title', [$event->getDisplayName()]), [
	'content' => $form,
	'filter' => false,
]);
