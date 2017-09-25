<?php

$question = elgg_extract('entity', $vars);
$value = elgg_extract('value', $vars);

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

echo elgg_view_field([
	'#type' => $input_type,
	'#label' => $question->title,
	'name' => 'question_' . $question->getGUID(),
	'value' => $value,
	'required' => (bool) $question->required,
	'options' => $question->getOptions(),
]);
