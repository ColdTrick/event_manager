<?php
/**
 * Form to search the attendees of an event
 *
 * @uses $vars['entity']       the event
 * @uses $vars['relationship'] which type of attendees to search for
 */

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof \Event) {
	return;
}

$relationship = elgg_extract('relationship', $vars);
if (!array_key_exists($relationship, $entity->getSupportedRelationships())) {
	return;
}

elgg_import_esm('forms/event_manager/event/attendees');

echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'guid',
	'value' => $entity->guid,
]);

echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'relationship',
	'value' => $relationship,
]);

echo elgg_view_field([
	'#type' => 'text',
	'name' => 'q',
	'title' => elgg_echo('search'),
	'placeholder' => elgg_echo('event_manager:event:search_attendees'),
	'value' => elgg_extract('q', $vars, get_input('q')),
]);

$footer = elgg_view_field([
	'#type' => 'submit',
	'text' => elgg_echo('search'),
	'class' => 'hidden',
]);

elgg_set_form_footer($footer);
