<?php

$slot = elgg_extract('entity', $vars);
$participate = elgg_extract('participate', $vars);
$register_type = elgg_extract('register_type', $vars);
$show_owner_actions = elgg_extract('show_owner_actions', $vars, true);

if (!$slot instanceof \ColdTrick\EventManager\Event\Slot) {
	return;
}

$result = "<table class='mbs' id='" . $slot->guid . "'>";

$result .= "<tr><td rowspan='2' class='event_manager_program_slot_attending'>";

$slot_set = $slot->slot_set;

$checkbox_options = [
	'rel' => $slot_set,
	'name' => 'slotguid_'  . $slot->guid,
	'id' => 'slotguid_' . $slot->guid,
	'value' => '1',
	'class' => 'event_manager_program_participatetoslot'
];

$registered_for_slot = '&nbsp;';
$logged_in_user = elgg_get_logged_in_user_entity();
if ($logged_in_user instanceof \ElggUser) {
	if ($logged_in_user->hasRelationship($slot->guid, EVENT_MANAGER_RELATION_SLOT_REGISTRATION)) {
		if (!$participate) {
			$registered_for_slot = elgg_view_icon('check', ['title' => elgg_echo('event_manager:event:relationship:event_attending')]);
		} else {
			$checkbox_options['checked'] = 'checked';
			$registered_for_slot = elgg_view('input/checkbox', $checkbox_options);
		}
	} elseif ($participate && ($slot->hasSpotsLeft() || $register_type == 'waitinglist')) {
		$registered_for_slot = elgg_view('input/checkbox', $checkbox_options);
	}
} else {
	if ($participate && ($slot->hasSpotsLeft() || $register_type == 'waitinglist')) {
		$registered_for_slot = elgg_view('input/checkbox', $checkbox_options);
	} elseif (!empty($vars['member'])) {
		$member = get_entity($vars['member']);
		if ($member instanceof \ElggEntity && $member->hasRelationship($slot->guid, EVENT_MANAGER_RELATION_SLOT_REGISTRATION)) {
			$registered_for_slot = elgg_view_icon('check', ['title' => elgg_echo('event_manager:event:relationship:event_attending')]);
		}
	}
}

$result .= $registered_for_slot;

$start_time = $slot->start_time;
$end_time = $slot->end_time;

$result .= '</td>';
$result .= "<td class='event_manager_program_slot_time'>" . date('H:i', $start_time) . ' - ' . date('H:i', $end_time) . '</td>';
$result .= "<td class='event_manager_program_slot_details' rel='" . $slot->guid . "'>";
$result .= '<span><b>' . $slot->getDisplayName() . '</b></span>';

if (!empty($slot_set)) {
	$color = substr(sha1($slot_set, false), 0, 6);
	$result .= elgg_format_element('span', [
		'class' => 'event-manager-program-slot-set',
		'style' => "background: #{$color};"
	], $slot_set);
}

if ($slot->canEdit() && $show_owner_actions && ($participate == false)) {
	$edit_slot = elgg_view('output/url', [
		'href' => false,
		'rel' => $slot->guid,
		'data-colorbox-opts' => json_encode([
			'href' => elgg_normalize_url('ajax/view/event_manager/forms/program/slot?slot_guid=' . $slot->guid)
		]),
		'class' => 'event_manager_program_slot_edit elgg-lightbox',
		'text' => elgg_echo('edit')
	]);
	
	$delete_slot = elgg_view('output/url', [
		'href' => false,
		'class' => 'event_manager_program_slot_delete',
		'text' => elgg_echo('delete')
	]);
	
	$result .= " [ $edit_slot | $delete_slot ]";
}

$subtitle_data = [];
if ($slot->location) {
	$subtitle_data[] = $slot->location;
}

if (!empty($slot->max_attendees)) {
	if (($slot->max_attendees > 0) && (($slot->max_attendees - $slot->countRegistrations()) > 0)) {
		$subtitle_data[] = ($slot->max_attendees - $slot->countRegistrations()) . ' ' . strtolower(elgg_echo('event_manager:edit:form:spots_left'));
	} else {
		$subtitle_data[] = strtolower(elgg_echo('event_manager:edit:form:spots_left:full'));
		
		$event = $slot->getOwnerEntity();
		if ($event->waiting_list_enabled && ($slot->getWaitingUsers(true) > 0)) {
			$subtitle_data[] = $slot->getWaitingUsers(true) . elgg_echo('event_manager:edit:form:spots_left:waiting_list');
		}
	}
}

if (!empty($subtitle_data)) {
	$result .= elgg_format_element('div', ['class' => 'elgg-quiet'], implode(' - ', $subtitle_data));
}

$result .= '</td></tr>';

$description = $slot->description;
if (!empty($description)) {
	$result .= '<tr><td>&nbsp;</td><td>';
	$result .= elgg_format_element('div', ['class' => 'event_manager_program_slot_description'], elgg_view('output/text', [
		'value' => $description,
	]));
	$result .= '</td></tr>';
}
	
$result .= '</table>';

echo $result;
