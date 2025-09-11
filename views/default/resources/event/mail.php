<?php
/**
 * Allow an event creator to mail the attendees of the event
 */

use Elgg\Exceptions\Http\EntityNotFoundException;

if (!(bool) elgg_get_plugin_setting('event_mail', 'event_manager')) {
	// feature not enabled
	throw new EntityNotFoundException();
}

$guid = (int) elgg_extract('guid', $vars);
elgg_entity_gatekeeper($guid, 'object', \Event::SUBTYPE, true);

/* @var $entity \Event */
$entity = get_entity($guid);

elgg_set_page_owner_guid($entity->container_guid);

elgg_push_entity_breadcrumbs($entity);

echo elgg_view_page(elgg_echo('event_manager:mail:title', [$entity->getDisplayName()]), [
	'content' => elgg_view_form('event_manager/event/mail', ['sticky_enabled' => true], ['entity' => $entity]),
	'filter_id' => 'events/mail',
	'filter_value' => 'mail',
]);
