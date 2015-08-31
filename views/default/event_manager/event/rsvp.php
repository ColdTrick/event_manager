<?php 

$event = elgg_extract('entity', $vars);
if (empty($event)) {
	return;
}

if (!elgg_is_logged_in()) {
	return;
}

if (!$event->openForRegistration()) {
	return;
}

$event_relationship_options = event_manager_event_get_relationship_options();

$user_relation = $event->getRelationshipByUser();
	
$rsvp_options = "";

foreach ($event_relationship_options as $rel) {
	if (($rel == EVENT_MANAGER_RELATION_ATTENDING) || $event->$rel) {
		if ($rel == EVENT_MANAGER_RELATION_ATTENDING) {
			if (!$event->hasEventSpotsLeft() && !$event->waiting_list_enabled) {
				continue;
			}
		}
		
		if ($rel == $user_relation) {
			$icon = elgg_view_icon('checkmark', 'float-alt');
			$link = elgg_echo('event_manager:event:relationship:' . $rel);
			$rsvp_options .= elgg_format_element('li', ['class' => 'selected'], $icon . $link);
		} else {
			if ($rel != EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST) {
				$icon = elgg_view_icon('checkmark-hover', 'float-alt elgg-discoverable');
				$link = elgg_view('output/url', [
					'is_action' => true,
					'href' => 'action/event_manager/event/rsvp?guid=' . $event->getGUID() . '&type=' . $rel,
					'text' => elgg_echo('event_manager:event:relationship:' . $rel)
				]);
				$rsvp_options .= elgg_format_element('li', ['class' => 'elgg-discover'], $icon . $link);
			}
		}
	}
}

if ($user_relation) {
	$icon = elgg_view_icon('checkmark-hover', 'float-alt elgg-discoverable');
	$link = elgg_view('output/url', [
		'is_action' => true, 
		'href' => 'action/event_manager/event/rsvp?guid=' . $event->getGUID() . '&type=' . EVENT_MANAGER_RELATION_UNDO, 
		'text' => elgg_echo('event_manager:event:relationship:undo')
	]);
	$rsvp_options .= elgg_format_element('li', ['class' => 'elgg-discover'], $icon . $link);
}

if (empty($rsvp_options)) {
	return;
}

$button_text = elgg_echo('event_manager:event:rsvp');
if ($user_relation) {
	$button_text = "<b>$button_text</b>";
}

echo elgg_format_element('span', ['class' => 'event_manager_event_actions'], $button_text);
echo elgg_format_element('ul', ['class' => 'event_manager_event_actions_drop_down'], $rsvp_options);

