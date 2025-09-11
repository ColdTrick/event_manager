<?php
/**
 * Form fields for editing event details
 */

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('event_manager:edit:form:shortdescription'),
	'#help' => elgg_echo('event_manager:edit:form:shortdescription:help'),
	'name' => 'shortdescription',
	'value' => elgg_extract('shortdescription', $vars),
]);

echo elgg_view_field([
	'#type' => 'longtext',
	'#label' => elgg_echo('description'),
	'#help' => elgg_echo('event_manager:edit:form:description:help'),
	'name' => 'description',
	'value' => elgg_extract('description', $vars),
]);

echo elgg_view_field([
	'#type' => 'tags',
	'#label' => elgg_echo('tags'),
	'#help' => elgg_echo('event_manager:edit:form:tags:help'),
	'name' => 'tags',
	'value' => elgg_extract('tags', $vars),
]);

echo elgg_view('entity/edit/header', [
	'entity' => elgg_extract('entity', $vars),
	'entity_type' => 'object',
	'entity_subtype' => 'event',
]);

echo elgg_view('forms/event_manager/event/edit/event_type', $vars);

echo elgg_view_field([
	'#type' => 'switch',
	'#label' => elgg_echo('event_manager:edit:form:comments_on'),
	'#help' => elgg_echo('event_manager:edit:form:comments_on:help'),
	'name' => 'comments_on',
	'value' => $vars['comments_on'],
]);

echo elgg_view_field([
	'#type' => 'access',
	'#label' => elgg_echo('access'),
	'name' => 'access_id',
	'value' => $vars['access_id'],
]);

echo elgg_view_field([
	'#type' => 'container_guid',
	'entity_type' => 'object',
	'entity_subtype' => \Event::SUBTYPE,
	'value' => $vars['container_guid'],
]);
