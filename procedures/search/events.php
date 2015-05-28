<?php

$search = get_input('search');
$advanced_search = get_input('advanced_search');
$search_type = get_input('search_type');
$offset = (int) get_input('offset', 0);
$container_guid = get_input('container_guid');
$type = get_input('type');
$past_events = get_input('past_events');
$attending = get_input('attending');
$owning = get_input('owning');
$friendsattending = get_input('friendsattending');
$region = get_input('region');
$event_type = get_input('event_type');
$start_time = get_input('start_time');
$end_time = get_input('end_time');
$latitude = get_input("latitude");
$longitude = get_input("longitude");
$distance = array("latitude" => get_input("distance_latitude"),"longitude" => get_input("distance_longitude"));

$returnData['valid'] = 0;
$options = array();

if ($advanced_search) {
	$options['advanced'] = true;

	if ($attending) {
		$options['meattending'] = true;
	}

	if ($owning) {
		$options['owning'] = true;
	}

	if ($friendsattending) {
		$options['friendsattending'] = true;
	}

	if ($region != '-') {
		$options['region'] = $region;
	}

	if ($event_type != '-') {
		$options['event_type'] = $event_type;
	}

	if ($start_time) {
		$options['start_time'] = $start_time;
	}

	if ($end_time) {
		$options['end_time'] = $end_time;
	}

	if (empty($end_time) && empty($start_time) && empty($search)) {
		$options['past_events'] = false;
	} else {
		$options['past_events'] = true;
	}
} else {
	if ($past_events) {
		$options['past_events'] = true;
	}
}

if (!empty($container_guid)) {
	$options["container_guid"] = $container_guid;
}

$options['search_type'] = $search_type;
$options['query'] = $search;
$options['offset'] = $offset;

if ($search_type == 'list') {
	$limit = 10;
	$options['limit'] = $limit;
	$entities = event_manager_search_events($options);

	$returnData['content'] = elgg_view_entity_list($entities['entities'], array("count" => $entities['count'], "offset" => $offset, "limit" => $limit, 'full_view' => false, 'pagination' => false));

	if (($entities['count'] - ($offset + $limit)) > 0) {
		$returnData['content'] .= '<div id="event_manager_event_list_search_more" rel="' . ($offset + $limit) . '">';
		$returnData['content'] .= elgg_echo('event_manager:list:showmorevents') . ' (' . ($entities['count'] - ($offset + $limit)) . ')</div>';
	}

	if ($entities['count'] < 1) {
		$returnData['content'] .= elgg_echo('event_manager:list:noresults');
	}
} else {
	$options['latitude'] = $latitude;
	$options['longitude'] = $longitude;
	$options['distance'] = $distance;
	$options['limit'] = 50;

	$entities = event_manager_search_events($options);
	foreach ($entities['entities'] as $event) {
		if ($event->location) {
			elgg_push_context("maps");

			$returnData['markers'][] = array(
				'guid' => $event->getGUID(),
				'lat' => $event->getLatitude(),
				'lng' => $event->getLongitude(),
				'title' => $event->title,
				'html' => elgg_view_entity($event, array("full_view" => false)),
				'hasrelation' => $event->getRelationshipByUser(),
				'iscreator' => (($event->getOwnerGUID() == elgg_get_logged_in_user_guid()) ? 'owner' : null)
			);
			elgg_pop_context();
		}
	}
}

$returnData['count'] = $entities['count'];
$returnData['valid'] = 1;
$returnData['offset'] = $offset;

echo json_encode($returnData);

exit;
