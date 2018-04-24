<?php
/**
 * Show a list of attendees to an event
 *
 * @uses $vars['entity']       Event to show attendees for
 * @uses $vars['relationship'] which attendees to list
 */

$entity = elgg_extract('entity', $vars);
$relationship = elgg_extract('relationship', $vars);
if (!$entity instanceof Event || empty($relationship)) {
	return;
}

echo elgg_list_entities_from_relationship([
	'type_subtype_pairs' => [
		'user' => ELGG_ENTITIES_ANY_VALUE,
		'object' => [
			EventRegistration::SUBTYPE,
		],
	],
	'relationship_guid' => $entity->guid,
	'relationship' => $relationship,
	'no_results' => elgg_echo('notfound'),
	'order_by' => 'r.time_created DESC',
]);
