<?php

$day = elgg_extract('entity', $vars);
$participate = elgg_extract('participate', $vars);
$register_type = elgg_extract('register_type', $vars);
$details_only = elgg_extract('details_only', $vars);
$show_owner_actions = elgg_extract('show_owner_actions', $vars, true);

if (!$day instanceof \ColdTrick\EventManager\Event\Day) {
	return;
}

$can_edit = $day->canEdit();

$details = '';
if ($day->description) {
	$details .= '<div><b>' . elgg_echo('event_manager:edit:form:start_day') . ':</b> ' . event_manager_format_date($day->date) . '</div>';
}

$details .= $day->getDisplayName();

if ($can_edit && $show_owner_actions && ($participate == false)) {
	$edit_day = elgg_view('output/url', [
		'href' => false,
		'rel' => $day->guid,
		'data-colorbox-opts' => json_encode([
			'href' => elgg_normalize_url('ajax/view/event_manager/forms/program/day?day_guid=' . $day->guid)
		]),
		'class' => 'event_manager_program_day_edit elgg-lightbox',
		'text' => elgg_echo('edit'),
	]);

	$delete_day = elgg_view('output/url', [
		'href' => false,
		'class' => 'event_manager_program_day_delete',
		'text' => elgg_echo('delete'),
	]);
	
	$details .= " [ $edit_day | $delete_day ]";
}

if ($details_only) {
	echo $details;
	return;
}

$day_info = elgg_format_element('div', [
	'class' => 'event_manager_program_day_details pbs mbs elgg-divide-bottom',
	'rel' => $day->guid,
], $details);

$slots = '';
$daySlots = $day->getEventSlots();
if ($daySlots) {
	$member = elgg_extract('member', $vars);
	
	foreach ($daySlots as $slot) {
		$slots .= elgg_view('event_manager/program/elements/slot', [
			'entity' => $slot,
			'participate' => $participate,
			'show_owner_actions' => $show_owner_actions,
			'register_type' => $register_type,
			'member' => $member,
		]);
	}
}

if ($can_edit && $show_owner_actions && ($participate == false)) {
	$slots .= elgg_view('output/url', [
		'href' => false,
		'class' => 'elgg-button elgg-button-action event_manager_program_slot_add mll elgg-lightbox',
		'rel' => $day->guid,
		'data-colorbox-opts' => json_encode([
			'href' => elgg_normalize_url('ajax/view/event_manager/forms/program/slot?day_guid=' . $day->guid)
		]),
		'text' => elgg_echo('event_manager:program:slot:add'),
		'icon' => 'plus',
	]);
}

echo elgg_format_element('div', [
	'class' => ['event_manager_program_day'],
	'id' => 'day_' . $day->guid
], $day_info . $slots);
