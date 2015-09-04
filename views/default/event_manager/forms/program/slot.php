<?php

$day_guid = get_input('day_guid');
$slot_guid = get_input('slot_guid');

if ($day_guid && ($entity = get_entity($day_guid))) {
	// assume new slot mode
	if (!($entity instanceof EventDay)) {
		unset($entity);
	}

	$start_time = null;
	$end_time = null;
} elseif ($slot_guid && ($entity = get_entity($slot_guid))) {
	// assume slot edit mode
	if (!($entity instanceof EventSlot)) {
		unset($entity);
	}
}

if (!$entity || !$entity->canEdit()) {
	echo elgg_echo('error');
	return;
}

$guid = null;
$parent_guid = null;
$title = null;
$description = null;
$start_time = null;
$end_time = null;
$location = null;
$max_attendees = null;

if ($entity instanceof EventSlot) {
	// assume slot edit mode
	$guid = $entity->getGUID();
	$title = $entity->title;
	$start_time = $entity->start_time;
	$end_time = $entity->end_time;
	$location = $entity->location;
	$max_attendees = $entity->max_attendees;
	$description = $entity->description;
	$slot_set = $entity->slot_set;

	$related_days = $entity->getEntitiesFromRelationship([
		'relationship' => 'event_day_slot_relation',
		'inverse_relationship' => false,
		'limit' => 1,
	]);

	if ($related_days) {
		$parent_guid = $related_days[0]->getGUID();
	}
} else {
	// entity is a day
	$parent_guid = $entity->getGUID();
}

if (!isset($slot_set)) {
	$slot_set = 0;
}

$form_body .= elgg_view('input/hidden', [
	'name' => 'guid', 
	'value' => $guid
]);
$form_body .= elgg_view('input/hidden', [
	'name' => 'parent_guid', 
	'value' => $parent_guid
]);

$form_body .= '<table><tr>';

$form_body .= '<td><label>' . elgg_echo('title') . ' *</label></td>';
$form_body .= '<td>' . elgg_view('input/text', [
	'name' => 'title', 
	'value' => $title
]) . '</td>';

$form_body .= '</tr><tr>';

$form_body .= '<td><label>' . elgg_echo('event_manager:edit:form:start_time') . ' *</label></td>';
$form_body .= '<td>';
$form_body .= elgg_view('input/time', [
	'name' => 'start_time',	
	'value' => $start_time
]);
$form_body .= '</td>';

$form_body .= '</tr><tr>';

$form_body .= '<td><label>' . elgg_echo('event_manager:edit:form:end_time') . ' *</label></td>';
$form_body .= '<td>';
$form_body .= elgg_view('input/time', [
	'name' => 'end_time', 
	'value' => $end_time
]);
$form_body .= '</td>';

$form_body .= '</tr><tr>';

$form_body .= '<td><label>' . elgg_echo('event_manager:edit:form:location') . '</label></td>';
$form_body .= '<td>' . elgg_view('input/text', [
	'name' => 'location',
	'value' => $location
]) . '</td>';

$form_body .= '</tr><tr>';

$form_body .= '<td><label>' . elgg_echo('event_manager:edit:form:max_attendees') . '</label></td>';
$form_body .= '<td>' . elgg_view('input/text', [
	'name' => 'max_attendees',
	'value' => $max_attendees
]) . '</td>';

$form_body .= '</tr><tr>';

$form_body .= '<td><label>' . elgg_echo('description') . '</label></td>';
$form_body .= '<td>' .  elgg_view('input/plaintext', [
	'name' => 'description', 
	'value' => $description
]) . '</td>';

$form_body .= '</tr><tr>';

$form_body .= '<td><label>' . elgg_echo('event_manager:edit:form:slot_set') . '</label></td>';
$form_body .= '<td>';

$form_body .= elgg_view('input/radio', [
	'name' => 'slot_set', 
	'options' => [
		elgg_echo('event_manager:edit:form:slot_set:empty') => 0
	], 
	'value' => $slot_set
]);

// unique set names for this event
$metadata = elgg_get_metadata([
	'type' => 'object',
	'subtype' => EventSlot::SUBTYPE,
	'container_guids' => [$entity->container_guid],
	'metadata_names' => ['slot_set'],
	'limit' => false
]);

$metadata_values = metadata_array_to_values($metadata);

if (!empty($metadata_values)) {
	$metadata_values = array_unique($metadata_values);
	foreach ($metadata_values as $value) {
		$form_body .= elgg_view('input/radio', [
			'name' => 'slot_set', 
			'options' => [$value => $value], 
			'value' => $slot_set
		]);
	}
}

// optionally add a new set
$form_body .= elgg_view('input/text', ['id' => 'event-manager-new-slot-set-name']);
$form_body .= elgg_view('input/button', [
	'id' => 'event-manager-new-slot-set-name-button', 
	'value' => elgg_echo('event_manager:edit:form:slot_set:add'), 
	'class' => 'elgg-button-action'
]);

$form_body .= '<div class="elgg-subtext">' . elgg_echo('event_manager:edit:form:slot_set:description') . '</div>';
$form_body .= '</td>';

$form_body .= '</tr></table>';

$form_body .= elgg_view('input/submit', ['value' => elgg_echo('submit')]);

$form = elgg_view('input/form', [
	'id' => 'event_manager_form_program_slot',
	'name' => 'event_manager_form_program_slot',
	'action' => 'action/event_manager/slot/save',
	'body' => $form_body
]);

echo elgg_view_module('info', elgg_echo('event_manager:form:program:slot'), $form, ['id' => 'event-manager-program-slot-lightbox']);
