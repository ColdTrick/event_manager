<?php

$day_guid = get_input('day_guid');
$slot_guid = get_input('slot_guid');

if ($day_guid && ($entity = get_entity($day_guid))) {
	// assume new slot mode
	if (!($entity instanceof \ColdTrick\EventManager\Event\Day)) {
		unset($entity);
	}

	$start_time = null;
	$end_time = null;
} elseif ($slot_guid && ($entity = get_entity($slot_guid))) {
	// assume slot edit mode
	if (!($entity instanceof \ColdTrick\EventManager\Event\Slot)) {
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

if ($entity instanceof \ColdTrick\EventManager\Event\Slot) {
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

$form_body .= elgg_view_field([
	'#type' => 'hidden',
	'name' => 'guid',
	'value' => $guid,
]);
$form_body .= elgg_view_field([
	'#type' => 'hidden',
	'name' => 'parent_guid',
	'value' => $parent_guid,
]);

$form_body .= elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('title'),
	'name' => 'title',
	'value' => $title,
	'required' => true,
]);

$time_fields = [
	[
		'#type' => 'time',
		'#label' => elgg_echo('event_manager:edit:form:start_time'),
		'name' => 'start_time',
		'value' => $start_time,
		'required' => true,
	],
	[
		'#type' => 'time',
		'#label' => elgg_echo('event_manager:edit:form:end_time'),
		'name' => 'end_time',
		'value' => $end_time,
		'required' => true,
	],
];

$form_body .= elgg_view('input/fieldset', [
	'fields' => $time_fields,
	'align' => 'horizontal',
	'class' => 'mbm pbs',
]);

$form_body .= elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('event_manager:edit:form:location'),
	'name' => 'location',
	'value' => $location,
]);

$form_body .= elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('event_manager:edit:form:max_attendees'),
	'name' => 'max_attendees',
	'value' => $max_attendees,
]);

$form_body .= elgg_view_field([
	'#type' => 'plaintext',
	'#label' => elgg_echo('description'),
	'name' => 'description',
	'value' => $description,
	'rows' => 2,
]);

$slot_options = [
	elgg_echo('event_manager:edit:form:slot_set:empty') => 0,
];

// unique set names for this event
$metadata = elgg_get_metadata([
	'type' => 'object',
	'subtype' => \ColdTrick\EventManager\Event\Slot::SUBTYPE,
	'container_guids' => [$entity->container_guid],
	'metadata_names' => ['slot_set'],
	'limit' => false,
]);

$metadata_values = metadata_array_to_values($metadata);

if (!empty($metadata_values)) {
	$metadata_values = array_unique($metadata_values);
	foreach ($metadata_values as $value) {
		$slot_options[$value] = $value;
	}
}

$form_body .= elgg_view_field([
	'#type' => 'radio', 
	'#label' => elgg_echo('event_manager:edit:form:slot_set'), 
	'name' => 'slot_set',
	'options' => $slot_options,
	'value' => $slot_set,
]);

// optionally add a new set
$form_body .= elgg_view('input/text', ['id' => 'event-manager-new-slot-set-name']);
$form_body .= elgg_view('input/button', [
	'id' => 'event-manager-new-slot-set-name-button',
	'value' => elgg_echo('event_manager:edit:form:slot_set:add'),
	'class' => 'elgg-button-action',
]);

$form_body .= '<div class="elgg-subtext">' . elgg_echo('event_manager:edit:form:slot_set:description') . '</div>';

$form_body .= elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_echo('submit'),
]);

$form = elgg_view('input/form', [
	'id' => 'event_manager_form_program_slot',
	'name' => 'event_manager_form_program_slot',
	'action' => 'action/event_manager/slot/save',
	'body' => $form_body,
]);

elgg_load_js('lightbox');
elgg_load_css('lightbox');

echo elgg_view_module('info', elgg_echo('event_manager:form:program:slot'), $form, ['id' => 'event-manager-program-slot-lightbox']);
