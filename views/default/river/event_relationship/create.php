<?php

$item = elgg_extract('item', $vars);

$user = $item->getSubjectEntity();
$event = $item->getObjectEntity();

$subject_url = elgg_view('output/url', [
	'href' => $user->getURL(),
	'text' => $user->getDisplayName(),
]);
$event_url = elgg_view('output/url', [
	'href' => $event->getURL(),
	'text' => $event->getDisplayName(),
]);

$relationtype = $event->getRelationshipByUser($user->guid);

$string = elgg_echo("event_manager:river:event_relationship:create:{$relationtype}", [$subject_url, $event_url]);

echo elgg_view('river/elements/layout', [
	'item' => $item,
	'summary' => $string,
]);