<?php 

$question = elgg_extract('entity', $vars);
$value = elgg_extract('value', $vars);
$register = elgg_extract('register', $vars, false);
$actions = '';

if (empty($question) || !($question instanceof EventRegistrationQuestion)) {
	return;
}

if ($question->canEdit() && !$register) {
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
	
	$actions = $edit_question . ' ' . $delete_question;
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

$required = '';
$required_class = '';

if ($question->required) {
	$required = ' *';
	$required_class = 'required';
}

$result = '';
if (!$register) {
	$result = elgg_view_icon('cursor-drag-arrow', 'mrm');
}
$result .= '<label>' . $question->title . $required . '</label>' . $actions . '<br />';
$result .= elgg_view('input/' . $fieldtypes[$question->fieldtype], [
	'name' => 'question_' . $question->getGUID(), 
	'value' => $value, 
	'options' => $question->getOptions(), 
	'class' => $required_class
]);

$options = ['id' => 'question_' . $question->getGUID()];
if (!$register) {
	$options['class'] = 'elgg-module-popup';
}
echo elgg_format_element('li', $options, $result);
