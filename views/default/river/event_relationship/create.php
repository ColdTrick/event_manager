<?php

$item = elgg_extract('item', $vars);

$user = $item->getSubjectEntity();
$event = $item->getObjectEntity();

$subject_url = elgg_view_entity_url($user);
$event_url = elgg_view_entity_url($event);

$relationtype = $event->getRelationshipByUser($user->guid);

echo elgg_view('river/elements/layout', [
	'item' => $item,
	'summary' => elgg_echo("event_manager:river:event_relationship:create:{$relationtype}", [$subject_url, $event_url]),
	'metadata' => elgg_view_menu('river', [
		'item' => $item,
		'prepare_dropdown' => true,
	]),
]);
