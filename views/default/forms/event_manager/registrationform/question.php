<?php

$question = elgg_extract('entity', $vars);

$fieldtype = null;
$fieldoptions = null;
$required = null;
$guid = null;
$title = null;

if ($question instanceof \EventRegistrationQuestion) {
	// assume day edit mode
	$guid = $question->guid;
	$title = $question->title;
	$fieldtype = $question->fieldtype;
	$required = $question->required;
	$fieldoptions = $question->fieldoptions;
}

$disabled = empty($guid);

$fields = [
	[
		'#type' => 'hidden',
		'name' => "questions[{$guid}][guid]",
		'value' => $guid,
		'disabled' => $disabled,
	],
	[
		'#type' => 'text',
		'#label' => elgg_echo('event_manager:editregistration:question'),
		'#class' => 'mbs',
		'name' => "questions[{$guid}][questiontext]",
		'value' => $title,
		'placeholder' => elgg_echo('event_manager:registrationform:editquestion:text:placeholder'),
		'disabled' => $disabled,
	],
	[
		'#type' => 'checkbox',
		'#label' => elgg_echo('event_manager:registrationform:editquestion:required'),
		'name' => "questions[{$guid}][required]",
		'checked' => (bool) $required,
		'switch' => true,
		'default' => 0,
		'value' => 1,
		'disabled' => $disabled,
	],
	[
		'#type' => 'fieldset',
		'align' => 'horizontal',
		'fields' => [
			[
				'#type' => 'select',
				'#label' => elgg_echo('event_manager:editregistration:fieldtype'),
				'#class' => 'man',
				'class' => 'event_manager_registrationform_question_fieldtype',
				'value' => $fieldtype,
				'name' => "questions[{$guid}][fieldtype]",
				'options_values' => [
					'Textfield' => elgg_echo('event_manager:editregistration:fieldtype:text'),
					'Textarea' => elgg_echo('event_manager:editregistration:fieldtype:longtext'),
					'Dropdown' => elgg_echo('event_manager:editregistration:fieldtype:select'),
					'Radiobutton' => elgg_echo('event_manager:editregistration:fieldtype:radio'),
				],
				'disabled' => $disabled,
			],
			[
				'#type' => 'text',
				'#label' => elgg_echo('event_manager:editregistration:fieldoptions'),
				'#class' => [
					'event_manager_registrationform_select_options',
					'elgg-field-stretch',
					in_array($fieldtype, ['Radiobutton', 'Dropdown']) ? null : 'hidden',
				],
				'name' => "questions[{$guid}][fieldoptions]",
				'value' => $fieldoptions,
				'placeholder' => elgg_echo('event_manager:editregistration:commasepatared'),
				'disabled' => $disabled,
			],
		],
	],
];

$form_body = '';
foreach ($fields as $field) {
	$form_body .= elgg_view_field($field);
}

$delete_question = elgg_view('output/url', [
	'icon' => 'delete',
	'text' => false,
	'href' => false,
	'class' => 'event_manager_questions_delete',
	'confirm' => elgg_echo('deleteconfirm'),
	'is_action' => false,
]);

echo elgg_view_image_block(elgg_view_icon('arrows-alt', ['class' => 'link']), $form_body, ['image_alt' => $delete_question]);
