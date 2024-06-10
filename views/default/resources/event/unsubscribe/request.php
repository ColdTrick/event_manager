<?php

use Elgg\Exceptions\Http\BadRequestException;

$guid = (int) elgg_extract('guid', $vars);

elgg_entity_gatekeeper($guid, 'object', \Event::SUBTYPE);
$entity = get_entity($guid);

if (!$entity->register_nologin) {
	$exception = new BadRequestException();
	$exception->setRedirectUrl(REFERRER);
	throw $exception;
}

elgg_set_page_owner_guid($entity->getContainerGUID());

elgg_push_entity_breadcrumbs($entity);

$body = elgg_view_form('event_manager/event/unsubscribe', [], ['entity' => $entity]);

echo elgg_view_page(elgg_echo('event_manager:unsubscribe:title', [$entity->getDisplayName()]), [
	'content' => $body,
	'filter' => false,
]);
