<?php

$event = elgg_extract('entity', $vars);
if (!$event instanceof \Event) {
	return;
}

$relationships = $event->getRelationships(false, 'DESC');
if (empty($relationships)) {
	return;
}

$supported_relationships = $event->getSupportedRelationships();
$can_edit = $event->canEdit();

foreach ($supported_relationships as $rel => $label) {
	if (!array_key_exists($rel, $relationships)) {
		continue;
	}
	
	$members = $relationships[$rel];
	
	$rel_title = '';
	if ($can_edit) {

		// export action
		$rel_title .= elgg_view('output/url', [
			'is_action' => true,
			'href' => "action/event_manager/attendees/export?guid={$event->getGUID()}&rel={$rel}",
			'title' => elgg_echo('event_manager:event:exportattendees'),
			'text' => elgg_view_icon('download'),
			'class' => 'float-alt'
		]);
	}
	$rel_title .= $label . ' (' . count($members) . ')';
	
	$rel_content = '';
	foreach ($members as $member) {
		$member_entity = get_entity($member);
		$rel_content .= elgg_view_entity_icon($member_entity, 'small', ['event' => $event, 'class' => 'float mrs']);
	}
		
	echo elgg_view_module('info', $rel_title, $rel_content, ['class' => 'event-manager-event-view-attendees']);
}
