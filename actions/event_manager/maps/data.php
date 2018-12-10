<?php

use Elgg\Database\QueryBuilder;
use Elgg\Database\Clauses\WhereClause;

$latitude = get_input('latitude');
$longitude = get_input('longitude');
$lat_distance = get_input('distance_latitude');
$long_distance = get_input('distance_longitude');
$container_guid = (int) get_input('container_guid');

if (!isset($latitude) || !isset($longitude) || !isset($lat_distance) || !isset($long_distance)) {
	return [];
}

$container = get_entity($container_guid);

$latitude = (float) $latitude;
$longitude = (float) $longitude;
$lat_distance = (float) $lat_distance;
$long_distance = (float) $long_distance;
	
$lat_min = $latitude - $lat_distance;
$lat_max = $latitude + $lat_distance;
$long_min = $longitude - $long_distance;
$long_max = $longitude + $long_distance;

$entities = elgg_get_entities([
	'limit' => 50,
	'type' => 'object',
	'subtype' => 'event',
	'container_guid' => ($container instanceof ElggGroup) ? $container->guid : ELGG_ENTITIES_ANY_VALUE,
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
		[
			'name' => 'event_start',
			'value' => time(),
			'operand' => '>=',
		],
	],
]);

if (empty($entities)) {
	return elgg_ok_response();
}

$result = [];
foreach ($entities as $event) {
	$result['markers'][] = [
		'guid' => $event->guid,
		'lat' => $event->getLatitude(),
		'lng' => $event->getLongitude(),
		'title' => $event->title,
		'html' => elgg_view('event_manager/event/infowindow', ['entity' => $event]),
		'has_relation' => $event->getRelationshipByUser(),
		'iscreator' => (($event->getOwnerGUID() == elgg_get_logged_in_user_guid()) ? 'owner' : null)
	];
}

return elgg_ok_response($result);
