<?php

$event = elgg_extract('entity', $vars);
if (!$event) {
	return;
}

$relationships = $event->getRelationships();
if (empty($relationships)) {
	return;
}

$ordered_relationships = [
	EVENT_MANAGER_RELATION_ATTENDING,
	EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST
];

if (elgg_get_plugin_setting('rsvp_interested', 'event_manager') !== 'no') {
	$ordered_relationships[] = EVENT_MANAGER_RELATION_INTERESTED;
}

$can_edit = $event->canEdit();
if ($can_edit) {
	$ordered_relationships[] = EVENT_MANAGER_RELATION_ATTENDING_PENDING;
}

foreach ($ordered_relationships as $rel) {
	if (!array_key_exists($rel, $relationships)) {
		continue;
	}
	if (($rel == EVENT_MANAGER_RELATION_ATTENDING) || ($rel == EVENT_MANAGER_RELATION_ATTENDING_PENDING) || $event->$rel || ($rel == EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST &&  $event->canEdit() && $event->waiting_list_enabled)) {
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
			
			// add attendee search
			$search_box = elgg_view('input/text', [
				'name' => 'q',
				'class' => 'mrs hidden',
				'autocomplete' => 'off',
				'data-toggle-slide' => 0,
			]);
			$search_box .= elgg_view_icon('search', [
				'rel' => 'toggle',
				'data-toggle-selector' => '.event-manager-event-view-search-attendees .elgg-input-text',
				'class' => 'link',
			]);
				
			$rel_title .= elgg_format_element('span', [
				'class' => 'event-manager-event-view-search-attendees float-alt',
				'title' => elgg_echo('event_manager:event:search_attendees'),
			], $search_box);
				
		}
		$rel_title .= elgg_echo("event_manager:event:relationship:{$rel}:label") . ' (' . count($members) . ')';
		
		$rel_content = '';
		foreach ($members as $member) {
			$member_entity = get_entity($member);
			$member_info = elgg_view_entity_icon($member_entity, 'small', ['event' => $event, 'class' => 'float mrs']);
			
			if ($can_edit) {
				$rel = $member_entity->name;
				
				if ($member_entity instanceof ElggUser) {
					$rel .= ' ' . $member_entity->username;
				} else {
					$rel .= ' ' . $member_entity->email;
				}
				
				$member_info = elgg_format_element('span',[
					'class' => 'event-manager-event-view-attendee-info',
					'rel' => $rel
				], $member_info);
			}
			
			$rel_content .= $member_info;
		}
			
		echo elgg_view_module('info', $rel_title, $rel_content, ['class' => 'event-manager-event-view-attendees']);
	}
}
