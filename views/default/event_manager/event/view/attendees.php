<?php

$event = elgg_extract('entity', $vars);
if (!$event instanceof \Event) {
	return;
}

if (!$event->show_attendees && !$event->canEdit()) {
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
	$total = count($members);
	$member_limit = 10;
	
	$rel_title = elgg_view('output/url', [
		'text' => "{$label} ({$total})",
		'href' => elgg_generate_url('collection:object:event:attendees', [
			'guid' => $event->guid,
			'relationship' => $rel,
		]),
	]);
	
	$rel_content = '';
	
	$first_guids = array_slice($members, 0, $member_limit);
	$first_entities = elgg_get_entities(['guids' => $first_guids]);
	$ordered_entities = array_flip($first_guids);
	foreach ($first_entities as $member) {
		$ordered_entities[$member->guid] = $member;
	}
	
	foreach ($ordered_entities as $member) {
		$rel_content .= elgg_view_entity_icon($member, 'small', ['class' => ['mrs', 'mbs']]);
	}
	
	if ($total > $member_limit) {
		$remaining = $total - $member_limit;
		
		$rel_content .= elgg_view('output/url', [
			'text' => elgg_echo('event_manager:event:view:attendees:more', [$remaining]),
			'href' => elgg_generate_url('collection:object:event:attendees', [
				'guid' => $event->guid,
				'relationship' => $rel,
			]),
			'class' => [
				'elgg-button',
				'elgg-button-action',
			],
		]);
	}
	
	$module_vars = [];
	
	if ($can_edit) {
		// export action
		$module_vars['menu'] = elgg_view('output/url', [
			'href' => elgg_generate_action_url('event_manager/attendees/export', [
				'guid' => $event->guid,
				'rel' => $rel,
			]),
			'title' => elgg_echo('event_manager:event:exportattendees'),
			'icon' => 'download',
			'text' => elgg_echo('download'),
		]);
	}
			
	echo elgg_view_module('info', $rel_title, $rel_content, $module_vars);
}
