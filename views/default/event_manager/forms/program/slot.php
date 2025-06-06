<?php

$day_guid = (int) get_input('day_guid');
$slot_guid = (int) get_input('slot_guid');

$day = elgg_call(ELGG_IGNORE_ACCESS, function() use ($day_guid) {
	// days are unavailable if event is private
	return get_entity($day_guid);
});

$slot = elgg_call(ELGG_IGNORE_ACCESS, function() use ($slot_guid) {
	// slots are unavailable if event is private
	return get_entity($slot_guid);
});

if ($day instanceof \ColdTrick\EventManager\Event\Day) {
	$entity = $day;
	$start_time = null;
	$end_time = null;
} elseif ($slot instanceof \ColdTrick\EventManager\Event\Slot) {
	// assume slot edit mode
	$entity = $slot;
}

if (!$entity || !$entity->canEdit()) {
	echo elgg_echo('EntityPermissionsException');
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
$slot_set = 0;

if ($entity instanceof \ColdTrick\EventManager\Event\Slot) {
	// assume slot edit mode
	$guid = $entity->guid;
	$title = $entity->title;
	
	// special handling of time for BC reasons
	$start_time = (new \DateTime())->setTimestamp($entity->start_time - 1);
	$end_time = (new \DateTime())->setTimestamp($entity->end_time - 1);
	$location = $entity->location;
	$max_attendees = $entity->max_attendees;
	$description = $entity->description;
	$slot_set = $entity->slot_set ?? 0;

	$related_days = elgg_call(ELGG_IGNORE_ACCESS, function() use ($entity) {
		return $entity->getEntitiesFromRelationship([
			'relationship' => 'event_day_slot_relation',
			'inverse_relationship' => false,
			'limit' => 1,
		]);
	});

	if ($related_days) {
		$parent_guid = $related_days[0]->guid;
	}
} else {
	// entity is a day
	$parent_guid = $entity->guid;
}

$form_body = elgg_view_field([
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

$form_body .= elgg_view_field([
	'#type' => 'fieldset',
	'align' => 'horizontal',
	'fields' => [
		[
			'#type' => 'time',
			'#label' => elgg_echo('event_manager:edit:form:start_time'),
			'name' => 'start_time',
			'value' => $start_time,
			'required' => true,
			'timestamp' => true,
		],
		[
			'#type' => 'time',
			'#label' => elgg_echo('event_manager:edit:form:end_time'),
			'name' => 'end_time',
			'value' => $end_time,
			'required' => true,
			'timestamp' => true,
		],
	],
]);

$form_body .= elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('event_manager:edit:form:location'),
	'name' => 'location',
	'value' => $location,
]);

$form_body .= elgg_view_field([
	'#type' => 'number',
	'#label' => elgg_echo('event_manager:edit:form:max_attendees'),
	'name' => 'max_attendees',
	'value' => $max_attendees,
	'min' => 0,
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

foreach ($metadata as $md) {
	$md_value = (array) $md->value;
	foreach ($md_value as $value) {
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
$form_body .= elgg_view_field([
	'#type' => 'fieldset',
	'#help' => elgg_echo('event_manager:edit:form:slot_set:description'),
	'align' => 'horizontal',
	'fields' => [
		[
			'#type' => 'text',
			'id' => 'event-manager-new-slot-set-name',
		],
		[
			'#type' => 'button',
			'id' => 'event-manager-new-slot-set-name-button',
			'text' => elgg_echo('event_manager:edit:form:slot_set:add'),
			'class' => 'elgg-button-action',
		]
	],
]);

$form_body .= elgg_view_field([
	'#type' => 'submit',
	'text' => elgg_echo('submit'),
]);

$form = elgg_view('input/form', [
	'id' => 'event_manager_form_program_slot',
	'name' => 'event_manager_form_program_slot',
	'action' => 'action/event_manager/slot/save',
	'body' => $form_body,
]);

echo elgg_view_module('info', elgg_echo('event_manager:form:program:slot'), $form, ['id' => 'event-manager-program-slot-lightbox']);
