<?php

$search = get_input('search');
$advanced_search = get_input('advanced_search');
$search_type = get_input('search_type');
$offset = (int) get_input('offset', 0);
$container_guid = (int) get_input('container_guid');
$type = get_input('type');
$past_events = get_input('past_events');
$attending = get_input('attending');
$owning = get_input('owning');
$friendsattending = get_input('friendsattending');
$region = get_input('region');
$event_type = get_input('event_type');
$event_start = get_input('event_start');
$event_end = get_input('event_end');
$latitude = get_input('latitude');
$longitude = get_input('longitude');
$distance = [
	'latitude' => get_input('distance_latitude'),
	'longitude' => get_input('distance_longitude')
];

$options = [
	'search_type' => $search_type,
	'query' => $search,
	'offset' => $offset
];

if ($advanced_search) {
	$options['advanced'] = true;
	$options['meattending'] = (bool) $attending;
	$options['owning'] = (bool) $owning;
	$options['friendsattending'] = (bool) $friendsattending;
	
	if ($region !== '-') {
		$options['region'] = $region;
	}

	if ($event_type !== '-') {
		$options['event_type'] = $event_type;
	}

	if (!empty($event_start)) {
		$options['event_start'] = $event_start;
	}

	if (!empty($event_end)) {
		$options['event_end'] = $event_end;
	}

	if (empty($event_end) && empty($event_start) && empty($search)) {
		$options['past_events'] = false;
	} else {
		$options['past_events'] = true;
	}
} else {
	$options['past_events'] = (bool) $past_events;
}

if (!empty($container_guid)) {
	$options['container_guid'] = $container_guid;
	
	elgg_set_page_owner_guid($container_guid);
}

if ($search_type == 'list') {
	$limit = 10;
	$options['limit'] = $limit;
	$entities = event_manager_search_events($options);

	$result['content'] = elgg_view_entity_list($entities['entities'], [
		'count' => $entities['count'],
		'offset' => $offset,
		'limit' => $limit,
		'full_view' => false,
		'pagination' => false
	]);

	if (($entities['count'] - ($offset + $limit)) > 0) {
		$result['content'] .= '<div id="event_manager_event_list_search_more" rel="' . ($offset + $limit) . '">';
		$result['content'] .= elgg_echo('event_manager:list:showmorevents') . ' (' . ($entities['count'] - ($offset + $limit)) . ')</div>';
	}

	if ($entities['count'] < 1) {
		$result['content'] .= elgg_echo('event_manager:list:noresults');
	}
} else {
	$options['latitude'] = $latitude;
	$options['longitude'] = $longitude;
	$options['distance'] = $distance;
	$options['limit'] = 50;

	$entities = event_manager_search_events($options);
	foreach ($entities['entities'] as $event) {
		if (!$event->location) {
			continue;
		}

		elgg_push_context('maps');

		$result['markers'][] = array(
			'guid' => $event->getGUID(),
			'lat' => $event->getLatitude(),
			'lng' => $event->getLongitude(),
			'title' => $event->title,
			'html' => elgg_view_entity($event, ['full_view' => false]),
			'has_relation' => $event->getRelationshipByUser(),
			'iscreator' => (($event->getOwnerGUID() == elgg_get_logged_in_user_guid()) ? 'owner' : null)
		);
		elgg_pop_context();
	}
}

$result['count'] = $entities['count'];
$result['offset'] = $offset;

echo json_encode($result);
