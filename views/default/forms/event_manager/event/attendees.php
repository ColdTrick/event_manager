<?php
/**
 * Form to search the attendees of an event
 *
 * @uses $vars['entity']       the event
 * @uses $vars['relationship'] which type of attendees to search for
 */

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof Event) {
	return;
}

$relationship = elgg_extract('relationship', $vars);
$valid_relationships = $entity->getSupportedRelationships();
if (!array_key_exists($relationship, $valid_relationships)) {
	return;
}

elgg_require_js('event_manager/attendees');

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
	'value' => elgg_echo('search'),
	'class' => 'hidden',
]);

elgg_set_form_footer($footer);
