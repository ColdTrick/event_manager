<?php

$event_guid = elgg_extract('event_guid', $vars);
$question_guid = elgg_extract('question_guid', $vars);
$ia = false;

if ($event_guid && ($entity = get_entity($event_guid))) {
	// assume new question mode
	if (!($entity instanceof Event)) {
		unset($entity);
	} elseif ($entity->canEdit()) {
		// Have to do this because of private event
		$ia = elgg_set_ignore_access(true);
	}
} elseif ($question_guid) {
	// Have to do this because of private event
	$ia = elgg_set_ignore_access(true);
	$entity = get_entity($question_guid);
	$associated_event = get_entity($entity->container_guid);
	
	// assume question edit mode and check access
	if(!($entity instanceof EventRegistrationQuestion) || ! $associated_event->canEdit()){
		unset($entity);
	}
}

$fieldtype = null;
$fieldoptions = null;
$required = null;
$guid = null;

if ($entity instanceof EventRegistrationQuestion) {
	// assume day edit mode
	$guid = $entity->getGUID();
	$event_guid = $entity->owner_guid;
	$title = $entity->title;
	$fieldtype = $entity->fieldtype;
	$required = $entity->required;
	$fieldoptions = $entity->fieldoptions;
} else {
	$event_guid	= $entity->getGUID();
}

if (empty($entity) || !$entity->canEdit()) {
	if ($ia) {
		elgg_set_ignore_access($ia);
	}
	
	echo elgg_echo('unknown_error');
	return;
}

if (empty($title)) {
	$title = elgg_echo('event_manager:editregistration:addfield:title');
}

$form_body = elgg_view('input/hidden', ['name' => 'event_guid', 'value' => $event_guid]);
$form_body .= elgg_view('input/hidden', ['name' => 'question_guid', 'value' => $question_guid]);

$form_body .= elgg_view_input('text', [
	'label' => elgg_echo('event_manager:editregistration:question'),
	'name' => 'questiontext',
	'value' => $title,
]);

$form_body .= elgg_view_input('select', [
	'label' => elgg_echo('event_manager:editregistration:fieldtype'),
	'id' => 'event_manager_registrationform_question_fieldtype',
	'value' => $fieldtype,
	'name' => 'fieldtype',
	'options' => ['Textfield', 'Textarea', 'Dropdown', 'Radiobutton'],
]);

$field_class = ['event_manager_registrationform_select_options'];
if (!in_array($fieldtype, ['Radiobutton', 'Dropdown'])) {
	$field_class[] = 'hidden';
}

$form_body .= elgg_view_input('text', [
	'label' => elgg_echo('event_manager:editregistration:fieldoptions'),
	'name' => 'fieldoptions',
	'value' => $fieldoptions,
	'help' => elgg_echo('event_manager:editregistration:commasepetared'),
	'field_class' => $field_class,
]);

$form_body .= elgg_view_input('checkboxes', [
	'name' => 'required',
	'value' => $required,
	'options' => [elgg_echo('event_manager:registrationform:editquestion:required') => '1'],
]);

$form_body .= elgg_view_input('submit', ['value' => elgg_echo('submit')]);

echo elgg_view_module('info', $title, $form_body, ['id' => 'event_manager_registrationform_lightbox']);

if ($ia) {
	elgg_set_ignore_access($ia);
}
