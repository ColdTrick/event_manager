<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof Event) {
	return;
}

if (empty($entity->getLatitude()) || empty($entity->getLongitude())) {
	return;
}

echo elgg_view('output/url', [
	'href' => 'www.openstreetmap.org/directions?engine=osrm_car&route=%3B' . $entity->getLatitude() . '%2C' . $entity->getLongitude(),
	'text' => elgg_echo('event_manager:event:location:plan_route'),
	'target' => '_blank',
	'class' => 'mlm',
]);
