<?php
use Elgg\Database\Clauses\OrderByClause;

/**
 * Show a list of attendees to an event
 *
 * @uses $vars['entity']       Event to show attendees for
 * @uses $vars['relationship'] which attendees to list
 */

$guid = get_input('guid');
$entity = elgg_extract('entity', $vars);
$relationship = elgg_extract('relationship', $vars, get_input('relationship'));

if (!$entity && $guid) {
	$entity = get_entity($guid);
}

if (!$entity instanceof Event || empty($relationship)) {
	return;
}

$options = [
	'type' => 'user', // trigger search fields generation
	'type_subtype_pairs' => [
		'user' => ELGG_ENTITIES_ANY_VALUE,
		'object' => [
			EventRegistration::SUBTYPE,
		],
	],
	'relationship_guid' => $entity->guid,
	'relationship' => $relationship,
	'no_results' => true,
	'order_by' => new OrderByClause('r.time_created', 'DESC'),
];

$getter = 'elgg_get_entities';

// searching ?
$q = elgg_extract('q', $vars, get_input('q'));
if (!empty($q)) {
	$options['query'] = $q;
	// fix pagination
	$options['base_url'] = elgg_generate_url('collection:object:event:attendees', [
		'guid' => $entity->guid,
		'relationship' => $relationship,
		'q' => $q,
	]);
		
	$getter = 'elgg_search';
}

echo elgg_list_entities($options, $getter);
