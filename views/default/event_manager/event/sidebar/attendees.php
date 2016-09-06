<?php
$event = elgg_extract("entity", $vars);
if (!$event) {
	return;
}

$relationships = $event->getRelationships();
if (empty($relationships)) {
	$relationships = [];
}

$organizer_guids = $event->organizer_guids;
if (!empty($organizer_guids)) {
	if (!array_key_exists(EVENT_MANAGER_RELATION_ORGANIZING, $relationships)) {
		$relationships[EVENT_MANAGER_RELATION_ORGANIZING] = [];
	}
	if (!is_array($organizer_guids)) {
		$organizer_guids = [$organizer_guids];
	}
	foreach ($organizer_guids as $organizer_guid) {
		$relationships[EVENT_MANAGER_RELATION_ORGANIZING][] = $organizer_guid;
	}
}

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

	$rel_title = elgg_echo("event_manager:event:relationship:{$rel}:label");

	$rel_content = '';
	foreach ($members as $member) {
		$member_entity = get_entity($member);
		if (empty($member_entity)) {
			continue;
		}
		$member_icon = elgg_view_entity_icon($member_entity, 'tiny', ['event' => $event]);
		$member_name = elgg_view('output/url', [
			'href' => $member_entity->getURL(),
			'text' => $member_entity->name,
		]);
		$rel_content .= elgg_view_image_block($member_icon, $member_name, ['class' => 'pan']);
	}

	echo elgg_view_module('aside', $rel_title, $rel_content);
}
