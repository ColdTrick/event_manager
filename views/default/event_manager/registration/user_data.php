<?php

$event = elgg_extract('event', $vars);
$entity = elgg_extract('entity', $vars);

if (empty($event) || empty($entity)) {
	return;
}

$questions = $event->getRegistrationFormQuestions();
$show_title = elgg_extract('show_title', $vars, false);

if (empty($questions)) {
	return;
}

if ($show_title) {
	echo elgg_format_element('h3', [], elgg_echo('event_manager:registration:view:information'));
}

if (($entity->guid !== elgg_get_logged_in_user_guid()) && !$entity instanceof \ElggUser) {
	echo elgg_format_element('label', [], elgg_echo('user:name:label'));
	echo elgg_format_element('div', ['class' => 'mbm'], $entity->getDisplayName());
	
	echo elgg_format_element('label', [], elgg_echo('email'));
	echo elgg_format_element('div', ['class' => 'mbm'], $entity->email);
}

/** @var EventRegistrationQuestion $question */
foreach ($questions as $question) {
	$answer_value = (string) $question->getAnswerFromUser($entity->guid)?->value;
	
	echo elgg_format_element('label', [], $question->getDisplayName());
	echo elgg_format_element('div', ['class' => 'mbm'], $answer_value);
}
