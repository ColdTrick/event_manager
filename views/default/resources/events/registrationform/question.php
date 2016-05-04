<?php

$event_guid = get_input('event_guid');
$question_guid = get_input('question_guid');

echo elgg_view_form('event_manager/registrationform/question', [
	'id' => 'event_manager_registrationform_question',
	'name' => 'event_manager_registrationform_question',
	'action' => 'javascript:elgg.event_manager.edit_questions_add_field($(\'#event_manager_registrationform_question\'))',
], [
	'event_guid' => $event_guid,
	'question_guid' => $question_guid,
]);
