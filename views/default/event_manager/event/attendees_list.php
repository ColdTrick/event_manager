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

$options = [
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
];

// searching ?
$q = elgg_extract('q', $vars, get_input('q'));
if (!empty($q)) {
	// fix pagination
	$options['base_url'] = elgg_http_add_url_query_elements("events/event/attendees/{$entity->guid}/{$relationship}", [
		'q' => $q,
	]);
	
	// search
	$dbprefix = elgg_get_config('dbprefix');
	$q = sanitize_string($q);
	
	$options['joins'] = [
		"LEFT OUTER JOIN {$dbprefix}users_entity ue ON e.guid = ue.guid",
		"LEFT OUTER JOIN {$dbprefix}metadata md ON e.guid = md.entity_guid",
		"JOIN {$dbprefix}metastrings msn ON md.name_id = msn.id",
		"JOIN {$dbprefix}metastrings msv ON md.value_id = msv.id",
	];
	
	$wheres = [
		"e.type = 'user' AND ue.name LIKE '%{$q}%'",
		"e.type = 'user' AND ue.username LIKE '%{$q}%'",
		"e.type = 'object' AND msn.string = 'name' AND msv.string LIKE '%{$q}%'",
	];
	
	if ($entity->canEdit()) {
		$wheres[] = "e.type = 'object' AND msn.string = 'email' AND msv.string LIKE '%{$q}%'";
	}
	
	$options['wheres'] = '((' . implode(') OR (', $wheres) . '))';
}

echo elgg_list_entities($options);
