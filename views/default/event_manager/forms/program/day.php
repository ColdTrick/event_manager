<?php

$event_guid = get_input('event_guid');
$day_guid = get_input('day_guid');

if ($event_guid && ($entity = get_entity($event_guid))) {
	// assume new day mode
	if (!($entity instanceof Event)) {
		unset($entity);
	}

} elseif ($day_guid && ($entity = get_entity($day_guid))) {
	// assume day edit mode
	if (!($entity instanceof \ColdTrick\EventManager\Event\Day)) {
		unset($entity);
	}
}

if (!$entity || !$entity->canEdit()) {
	// @todo nice error message
	echo elgg_echo('error');
	return;
}

$guid = null;
$description = null;
$title = null;

if ($entity instanceof \ColdTrick\EventManager\Event\Day) {
	// assume day edit mode
	$guid = $entity->getGUID();
	$parent_guid = $entity->owner_guid;
	$title = $entity->title;
	$description = $entity->description;
	$date = $entity->date;
} else {
	// entity is a event
	$parent_guid = $entity->getGUID();

	// make nice default date
	$days = $entity->getEventDays();
	$last_day = end($days);
	if (!$last_day) {
		$date = $entity->getStartTimestamp() + (3600 * 24);
	} else {
		$date = $last_day->date + (3600 * 24);
	}
}

$form_body = elgg_view_field([
	'#type' => 'date',
	'#label' => elgg_echo('event_manager:edit:form:start_day'),
	'name' => 'date',
	'id' => 'date',
	'timestamp' => true,
	'value' => $date,
	'required' => true,
]);

$form_body .= elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('title'),
	'name' => 'description',
	'value' => $description
]);

$form_body .= elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('description'),
	'name' => 'title',
	'value' => $title,
]);

$form_body .= elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_echo('submit'),
	'class' => 'mtm',
]);
$form_body .= elgg_view_field([
	'#type' => 'hidden',
	'name' => 'guid',
	'value' => $guid
]);
$form_body .= elgg_view_field([
	'#type' => 'hidden',
	'name' => 'parent_guid',
	'value' => $parent_guid
]);

$body = elgg_view('input/form', [
	'id' => 'event_manager_form_program_day',
	'name' => 'event_manager_form_program_day',
	'action' => 'javascript:elgg.event_manager.program_add_day($(\'#event_manager_form_program_day\'))',
	'body' => $form_body,
]);

elgg_load_js('lightbox');
elgg_load_css('lightbox');

echo elgg_view_module('info', elgg_echo('event_manager:form:program:day'), $body, ['id' => 'event-manager-program-day-lightbox']);
