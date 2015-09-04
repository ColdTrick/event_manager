<?php

$day = elgg_extract('entity', $vars);
$participate = elgg_extract('participate', $vars);
$register_type = elgg_extract('register_type', $vars);
$details_only = elgg_extract('details_only', $vars);

if (empty($day) || !($day instanceof EventDay)) {
	return;
}

$can_edit = $day->canEdit();

$details = '';
if ($description = $day->description) {
	$details .= '<div><b>' . elgg_echo('event_manager:edit:form:start_day') . ':</b> ' . event_manager_format_date($day->date) . '</div>';
}

$details .= $day->title;

if ($can_edit && !elgg_in_context('programmailview') && ($participate == false)) {
	$edit_day = elgg_view('output/url', [
		'href' => 'javascript:void(0);',
		'rel' => $day->getGUID(),
		'data-colorbox-opts' => json_encode([
			'href' => elgg_normalize_url('ajax/view/event_manager/forms/program/day?day_guid=' . $day->getGUID())
		]),
		'class' => 'event_manager_program_day_edit elgg-lightbox',
		'text' => elgg_echo('edit')
	]);

	$delete_day = elgg_view('output/url', [
		'href' => 'javascript:void(0);',
		'class' => 'event_manager_program_day_delete',
		'text' => elgg_echo('delete')
	]);
	
	$details .= " [ $edit_day | $delete_day ]";
}

if ($details_only) {
	echo $details;
	return;
}

$day_info = elgg_format_element('div', [
	'class' => 'event_manager_program_day_details pbs mbs mll elgg-divide-bottom',
	'rel' => $day->getGUID()
], $details);

$slots = '';
if ($daySlots = $day->getEventSlots()) {
	foreach ($daySlots as $slot) {
		$slots .= elgg_view('event_manager/program/elements/slot', [
			'entity' => $slot, 
			'participate' => $participate, 
			'register_type' => $register_type, 
			'member' => $vars['member']
		]);
	}
}

if ($can_edit && !elgg_in_context('programmailview') && ($participate == false)) {	
	$slots .= elgg_view('output/url', [
		'href' => 'javascript:void(0);',
		'class' => 'elgg-button elgg-button-action event_manager_program_slot_add mll elgg-lightbox',
		'rel' => $day->getGUID(),
		'data-colorbox-opts' => json_encode([
			'href' => elgg_normalize_url('ajax/view/event_manager/forms/program/slot?day_guid=' . $day->getGUID())
		]),
		'text' => elgg_echo("event_manager:program:slot:add")
	]);
}

$classes = ['event_manager_program_day'];
if (!elgg_extract('selected', $vars)) {
	$classes[] = 'hidden';
}

echo elgg_format_element('div', [
	'class' => $classes,
	'id' => 'day_' . $day->getGUID()
], $day_info . $slots);
