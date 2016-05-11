<?php

$question = elgg_extract('entity', $vars);
$value = elgg_extract('value', $vars);
$register = elgg_extract('register', $vars, false);
$actions = '';

if (!($question instanceof EventRegistrationQuestion)) {
	return;
}

$fieldtypes = [
	'Textfield' => 'text',
	'Textarea' => 'plaintext',
	'Dropdown' => 'dropdown',
	'Radiobutton' => 'radio'
];

if (!array_key_exists($question->fieldtype, $fieldtypes)) {
	return;
}

$input_type = $fieldtypes[$question->fieldtype];

$field_options = [
	'label' => $question->title,
	'name' => 'question_' . $question->getGUID(),
	'value' => $value,
	'required' => (bool) $question->required,
	'options' => $question->getOptions(),
];

if ($register) {
	echo elgg_view_input($input_type, $field_options);
	return;
}

if (!$question->canEdit()) {
	return;
}

elgg_load_js('lightbox');
elgg_load_css('lightbox');

$edit_question = elgg_view('output/url', [
	'href' => 'javascript:void(0);',
	'text' => elgg_view_icon('settings-alt'),
	'class' => 'mlm elgg-lightbox',
	'data-colorbox-opts' => json_encode([
		'href' => elgg_normalize_url('events/registrationform/question?question_guid=' . $question->getGUID())
	]),
	'title' => elgg_echo('edit'),
]);

$delete_question = elgg_view('output/url', [
	'href' => 'javascript:void(0);',
	'text' => elgg_view_icon('delete'),
	'class' => 'event_manager_questions_delete',
	'title' => elgg_echo('delete'),
	'rel' => $question->getGUID(),
]);

$label = elgg_view_icon('cursor-drag-arrow', 'mrm');
$label .= $field_options['label'];
$label .= $edit_question . ' ' . $delete_question;

$field_options['label'] = $label;

$options = [
	'id' => 'question_' . $question->getGUID(),
	'class' => 'elgg-module-popup',
];

echo elgg_format_element('li', $options, elgg_view_input($input_type, $field_options));
