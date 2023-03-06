<?php
/**
 * Form fields for editing event details
 */

$entity = elgg_extract('entity', $vars);

$shortdescription = elgg_extract('shortdescription', $vars);
$description = elgg_extract('description', $vars);
$tags = elgg_extract('tags', $vars);

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('event_manager:edit:form:shortdescription'),
	'#help' => elgg_echo('event_manager:edit:form:shortdescription:help'),
	'name' => 'shortdescription',
	'value' => $shortdescription,
]);

echo elgg_view_field([
	'#type' => 'longtext',
	'#label' => elgg_echo('description'),
	'#help' => elgg_echo('event_manager:edit:form:description:help'),
	'name' => 'description',
	'value' => $description,
]);

echo elgg_view_field([
	'#type' => 'tags',
	'#label' => elgg_echo('tags'),
	'#help' => elgg_echo('event_manager:edit:form:tags:help'),
	'name' => 'tags',
	'value' => $tags,
]);

echo elgg_view('entity/edit/header', [
	'entity' => $entity,
	'entity_type' => 'object',
	'entity_subtype' => 'event',
]);

echo elgg_view('forms/event_manager/event/edit/event_type', $vars);

echo elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('event_manager:edit:form:comments_on'),
	'#help' => elgg_echo('event_manager:edit:form:comments_on:help'),
	'name' => 'comments_on',
	'checked' => (int) $vars['comments_on'] === 1,
	'switch' => true,
	'default' => '0',
	'value' => '1',
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
