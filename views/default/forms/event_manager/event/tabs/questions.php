<?php
$event = elgg_extract('entity', $vars);

// Have to do this for private events
$ia = elgg_set_ignore_access(true);

$list = '';
if ($event instanceof \Event) {
	$list = elgg_view_entity_list($event->getRegistrationFormQuestions(), [
		'list_class' => 'event_manager_registrationform_fields',
		'item_view' => 'forms/event_manager/registrationform/question',
	]);
}

if (!empty($list)) {
	echo $list;
} else {
	// add empty ul for fields to be added
	echo elgg_format_element('ul', [
		'class' => 'elgg-list event_manager_registrationform_fields',
	]);
}

echo elgg_format_element('li', [
	'id' => 'event-manager-registration-field-template',
	'class' => 'hidden elgg-item elgg-item-object elgg-item-object-eventregistrationquestion ui-sortable-handle',
], elgg_view('forms/event_manager/registrationform/question'));

echo elgg_view('output/url', [
	'href' => 'javascript:void(0);',
	'class' => 'elgg-button elgg-button-action event-manager-registration-add-field',
	'text' => elgg_echo('event_manager:editregistration:addfield'),
]);

elgg_set_ignore_access($ia);
