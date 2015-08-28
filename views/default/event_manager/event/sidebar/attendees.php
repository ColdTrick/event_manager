<?php
$event = elgg_extract("entity", $vars);
if (!$event) {
	return;
}

$relationships = $event->getRelationships();
if (empty($relationships)) {
	return;
}

$ordered_relationships = [
	EVENT_MANAGER_RELATION_PRESENTING,
	EVENT_MANAGER_RELATION_EXHIBITING,
	EVENT_MANAGER_RELATION_ORGANIZING,
];

foreach ($ordered_relationships as $rel) {
	if (!array_key_exists($rel, $relationships)) {
		continue;
	}
	
	$members = $relationships[$rel];

	$rel_title = elgg_echo("event_manager:event:relationship:{$rel}:label") . ' (' . count($members) . ')';

	$rel_content = '';
	foreach ($members as $member) {
		$rel_content .= elgg_view_entity_icon(get_entity($member), 'small', ['event' => $event, 'class' => 'mrs']);
	}

	echo elgg_view_module('aside', $rel_title, $rel_content, ['class' => 'event-manager-event-view-attendees']);
}
