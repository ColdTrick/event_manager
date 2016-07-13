<?php

$item = elgg_extract('item', $vars);

$user = $item->getSubjectEntity();
$event = $item->getObjectEntity();

$subject_url = elgg_view('output/url', [
	'href' => $user->getURL(),
	'text' => $user->name,
]);
$event_url = elgg_view('output/url', [
	'href' => $event->getURL(),
	'text' => $event->title,
]);

$relationtype = $event->getRelationshipByUser($user->getGUID());

$string = elgg_echo("event_manager:river:event_relationship:create:{$relationtype}", [$subject_url, $event_url]);

echo elgg_view('river/elements/layout', [
	'item' => $item,
	'summary' => $string,

	// truthy value to bypass responses rendering
	'responses' => ' ',
]);