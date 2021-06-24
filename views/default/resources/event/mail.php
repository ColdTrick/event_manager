<?php
/**
 * Allow an event creator to mail the attendees of the event
 */

use ColdTrick\EventManager\Forms\EventMail;
use Elgg\Exceptions\Http\EntityNotFoundException;
use Elgg\Exceptions\Http\EntityPermissionsException;

if (!(bool) elgg_get_plugin_setting('event_mail', 'event_manager')) {
	// feature not enabled
	throw new EntityNotFoundException();
}

$guid = (int) elgg_extract('guid', $vars);
elgg_entity_gatekeeper($guid, 'object', \Event::SUBTYPE);

/* @var $entity \Event */
$entity = get_entity($guid);
if (!$entity->canEdit()) {
	throw new EntityPermissionsException();
}

elgg_set_page_owner_guid($entity->container_guid);

elgg_push_entity_breadcrumbs($entity);

// make page elements
$form = new EventMail($entity);

$content = elgg_view_form('event_manager/event/mail', [], $form());

// draw page
echo elgg_view_page(elgg_echo('event_manager:mail:title', [$entity->getDisplayName()]), [
	'content' => $content,
	'filter_id' => 'events/mail',
	'filter_value' => 'mail',
]);
