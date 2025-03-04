<?php

$event_guid = (int) get_input('event_guid');
$day_guid = (int) get_input('day_guid');

$event = get_entity($event_guid);
$day = elgg_call(ELGG_IGNORE_ACCESS, function() use ($day_guid) {
	// days are unavailable if event is private
	return get_entity($day_guid);
});

if ($event instanceof \Event) {
	$entity = $event;
} elseif ($day instanceof \ColdTrick\EventManager\Event\Day) {
	// assume day edit mode
	$entity = $day;
}

if (!$entity || !$entity->canEdit()) {
	echo elgg_echo('EntityPermissionsException');
	return;
}

$guid = null;
$description = null;
$title = null;

if ($entity instanceof \ColdTrick\EventManager\Event\Day) {
	// assume day edit mode
	$guid = $entity->guid;
	$parent_guid = $entity->owner_guid;
	$title = $entity->title;
	$description = $entity->description;
	$date = $entity->date;
} else {
	// entity is a event
	$parent_guid = $entity->guid;

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
	'text' => elgg_echo('submit'),
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
	'action' => false,
	'body' => $form_body,
]);

echo elgg_view_module('info', elgg_echo('event_manager:form:program:day'), $body, ['id' => 'event-manager-program-day-lightbox']);
