<?php

use Elgg\Database\QueryBuilder;
use Elgg\Database\Clauses\WhereClause;

$latitude = get_input('latitude');
$longitude = get_input('longitude');
$lat_distance = get_input('distance_latitude');
$long_distance = get_input('distance_longitude');
$guid = (int) get_input('guid');
$resource = get_input('resource');
$tag = get_input('tag');

if (!isset($latitude) || !isset($longitude) || !isset($lat_distance) || !isset($long_distance)) {
	return [];
}

$entity = get_entity($guid);

$latitude = (float) $latitude;
$longitude = (float) $longitude;
$lat_distance = (float) $lat_distance;
$long_distance = (float) $long_distance;
	
$lat_min = $latitude - $lat_distance;
$lat_max = $latitude + $lat_distance;
$long_min = $longitude - $long_distance;
$long_max = $longitude + $long_distance;

$options = [
	'type' => 'object',
	'subtype' => \Event::SUBTYPE,
	'limit' => 50,
	'container_guid' => ($entity instanceof ElggGroup) ? $entity->guid : ELGG_ENTITIES_ANY_VALUE,
	'wheres' => [
		function (QueryBuilder $qb, $main_alias) use ($lat_min, $lat_max, $long_min, $long_max) {
			
			// cannot use between helper because of casting of values
			$lat_alias = $qb->joinMetadataTable($main_alias, 'guid', 'geo:lat');
			$long_alias = $qb->joinMetadataTable($main_alias, 'guid', 'geo:long');
			
			return $qb->merge([
				(new WhereClause("{$lat_alias}.value >= {$lat_min}"))->prepare($qb, $main_alias),
				(new WhereClause("{$lat_alias}.value <= {$lat_max}"))->prepare($qb, $main_alias),
				(new WhereClause("{$long_alias}.value >= {$long_min}"))->prepare($qb, $main_alias),
				(new WhereClause("{$long_alias}.value <= {$long_max}"))->prepare($qb, $main_alias),
			]);
		}
	],
	'metadata_name_value_pairs' => [
		'upcoming' => [
			'name' => 'event_start',
			'value' => time(),
			'operand' => '>=',
		],
	],
	'batch' => true,
];

// resource specific options
switch ($resource) {
	case 'live':
		unset($options['metadata_name_value_pairs']['upcoming']);
		
		$options['metadata_name_value_pairs'][] = [
			'name' => 'event_start',
			'value' => time(),
			'operand' => '<=',
		];
		$options['metadata_name_value_pairs'][] = [
			'name' => 'event_end',
			'value' => time(),
			'operand' => '>=',
		];
		break;
	case 'owner':
		if (!$entity instanceof ElggUser) {
			return elgg_error_response();
		}
		
		unset($options['metadata_name_value_pairs']['upcoming']);
		
		$options['owner_guid'] = $entity->guid;
		break;
	case 'attending':
		if (!$entity instanceof ElggUser) {
			return elgg_error_response();
		}
		
		$options['relationship'] = EVENT_MANAGER_RELATION_ATTENDING;
		$options['relationship_guid'] = $entity->guid;
		$options['inverse_relationship'] = true;
		break;
}

if (!empty($tag)) {
	$options['metadata_name_value_pairs'][] = [
		'name' => 'tags',
		'value' => $tag,
		'case_sensitive' => false,
	];
}

// let others extend this
$params = [
	'resource' => $resource,
	'guid' => $guid,
	'latitude' => $latitude,
	'longitude' => $longitude,
	'distance_latitude' => $lat_distance,
	'distance_longitude' => $long_distance,
];
$options = elgg_trigger_event_results('maps_data:options', 'event_manager', $params, $options);

// fetch data
$entities = elgg_get_entities($options);

$result = [];
foreach ($entities as $event) {
	$result['markers'][] = [
		'guid' => $event->guid,
		'lat' => $event->getLatitude(),
		'lng' => $event->getLongitude(),
		'title' => $event->title,
		'has_relation' => $event->getRelationshipByUser(),
		'iscreator' => (($event->getOwnerGUID() == elgg_get_logged_in_user_guid()) ? 'owner' : null)
	];
}

return elgg_ok_response($result);
