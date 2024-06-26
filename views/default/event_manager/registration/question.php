<?php

$question = elgg_extract('entity', $vars);
$value = elgg_extract('value', $vars);

if (!$question instanceof \EventRegistrationQuestion) {
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

echo elgg_view_field([
	'#type' => $fieldtypes[$question->fieldtype],
	'#label' => $question->getDisplayName(),
	'name' => 'question_' . $question->guid,
	'value' => $value,
	'required' => (bool) $question->required,
	'options' => $question->getOptions(),
]);
