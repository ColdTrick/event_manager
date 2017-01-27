<?php

$question = elgg_extract('entity', $vars);

$fieldtype = null;
$fieldoptions = null;
$required = null;
$guid = null;
$title = null;

if ($question instanceof \EventRegistrationQuestion) {
	// assume day edit mode
	$guid = $question->getGUID();
	$title = $question->title;
	$fieldtype = $question->fieldtype;
	$required = $question->required;
	$fieldoptions = $question->fieldoptions;
}

$disabled = empty($guid);
$form_body = '';
$form_body .= elgg_view('input/hidden', [
	'name' => "questions[{$guid}][guid]",
	'value' => $guid,
	'disabled' => $disabled,
]);

$question_label = elgg_echo('event_manager:editregistration:question');
$question_label .= elgg_view_field([
	'#type' => 'checkboxes',
	'#class' => 'float-alt man elgg-subtext',
	'name' => "questions[{$guid}][required]",
	'value' => $required,
	'options' => [elgg_echo('event_manager:registrationform:editquestion:required') => '1'],
	'default' => false,
	'disabled' => $disabled,
]);
$form_body .= elgg_view_field([
	'#type' => 'text',
	'#label' => $question_label,
	'#class' => 'mbs',
	'name' => "questions[{$guid}][questiontext]",
	'value' => $title,
	'placeholder' => elgg_echo('event_manager:registrationform:editquestion:text:placeholder'),
	'disabled' => $disabled,
]);

$form_body .= '<div class="elgg-col elgg-col-1of4">';
$form_body .= elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('event_manager:editregistration:fieldtype'),
	'#class' => 'man',
	'class' => 'event_manager_registrationform_question_fieldtype',
	'value' => $fieldtype,
	'name' => "questions[{$guid}][fieldtype]",
	'options' => ['Textfield', 'Textarea', 'Dropdown', 'Radiobutton'],
	'disabled' => $disabled,
]);

$form_body .= '</div>';
$form_body .= '<div class="elgg-col elgg-col-3of4">';
$field_class = ['event_manager_registrationform_select_options', 'man'];
if (!in_array($fieldtype, ['Radiobutton', 'Dropdown'])) {
	$field_class[] = 'hidden';
}
$form_body .= elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('event_manager:editregistration:fieldoptions'),
	'#class' => $field_class,
	'name' => "questions[{$guid}][fieldoptions]",
	'value' => $fieldoptions,
	'placeholder' => elgg_echo('event_manager:editregistration:commasepatared'),
	'disabled' => $disabled,
]);
$form_body .= '</div>';

$delete_question = elgg_view('output/url', [
	'href' => 'javascript:void(0);',
	'text' => elgg_view_icon('delete'),
	'class' => 'event_manager_questions_delete',
	'confirm' => elgg_echo('deleteconfirm'),
	'is_action' => false,
]);

echo elgg_view_image_block(elgg_view_icon('arrows', 'link'), $form_body, ['image_alt' => $delete_question]);
