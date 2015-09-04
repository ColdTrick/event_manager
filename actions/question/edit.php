<?php

$event_guid = get_input('event_guid');
$question_guid = get_input('question_guid');

$fieldtype = get_input('fieldtype');
$fieldoptions = get_input('fieldoptions');
$questiontext = get_input('questiontext');
$required = get_input('required');

$event = get_entity($event_guid);

// Have to do this for private events
$ia = elgg_set_ignore_access(true);

if (!($event instanceof Event) || !($event->canEdit())) {
	elgg_set_ignore_access($ia);
	return;
}

$question = get_entity($question_guid);
if ($question_guid && !($question instanceof EventRegistrationQuestion)) {
	register_error('not a question');
	return;
}

if (empty($question)) {
	$question = new EventRegistrationQuestion();
}

$question->title = $questiontext;
$question->container_guid = $event->getGUID();
$question->owner_guid = $event->getGUID();
$question->access_id = $event->access_id;

if ($question->save()) {
	$question->fieldtype = $fieldtype;
	$question->required = $required;
	$question->fieldoptions = $fieldoptions;

	if (empty($question_guid)) {
		$question->order = $event->getRegistrationFormQuestions(true);
	}

	$question->addRelationship($event->getGUID(), 'event_registrationquestion_relation');

	echo elgg_view('event_manager/registration/question', [
		'entity' => $question
	]);
}
		
elgg_set_ignore_access($ia);
