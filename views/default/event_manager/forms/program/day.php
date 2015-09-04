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
	if (!($entity instanceof EventDay)) {
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

if ($entity instanceof EventDay) {
	// assume day edit mode
	$guid = $entity->getGUID();
	$parent_guid = $entity->owner_guid;
	$title = $entity->title;
	$description = $entity->description;
	$date = $entity->date;
	if (!empty($date)) {
		$date = event_manager_format_date($date);
	}
} else {
	// entity is a event
	$parent_guid = $entity->getGUID();

	// make nice default date
	$days = $entity->getEventDays();
	$last_day = end($days);
	if (!$last_day) {
		$date = ($entity->start_day + (3600 * 24));
	} else {
		$date = ($last_day->date + (3600 * 24));
	}

	$date = event_manager_format_date($date);
}

$form_body .= '<div>';

$form_body .= elgg_view('input/hidden', [
	'name' => 'guid', 
	'value' => $guid
]);
$form_body .= elgg_view('input/hidden', [
	'name' => 'parent_guid', 
	'value' => $parent_guid
]);

$form_body .= '<label>' . elgg_echo('event_manager:edit:form:start_day') . ' *</label><br />';
$form_body .= elgg_view('input/date', [
	'name' => 'date',
	'id' => 'date',
	'value' => $date
]) . '<br />';

$form_body .= '<label>' . elgg_echo('title') . '</label><br />';
$form_body .= elgg_view('input/text', [
	'name' => 'description',
	'value' => $description
]);

$form_body .= '<label>' . elgg_echo('description') . '</label><br />';
$form_body .= elgg_view('input/text', [
	'name' => 'title',
	'value' => $title
]);

$form_body .= elgg_view('input/submit', [
	'value' => elgg_echo('submit'),
	'class' => 'elgg-button-submit mtm'
]);
$form_body .= '</div>';

$body = elgg_view('input/form', [
	'id' => 'event_manager_form_program_day',
	'name' => 'event_manager_form_program_day',
	'action' => 'javascript:elgg.event_manager.program_add_day($(\'#event_manager_form_program_day\'))',
	'body' => $form_body
]);

echo elgg_view_module('info', elgg_echo('event_manager:form:program:day'), $body, ['id' => 'event-manager-program-day-lightbox']);
