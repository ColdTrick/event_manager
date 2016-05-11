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

if (($entity->guid != elgg_get_logged_in_user_guid()) && !($entity instanceof \ElggUser)) {
	echo '<label>' . elgg_echo('user:name:label') . '</label>';
	echo '<div class="mbm">' . $entity->name . '</div>';
	
	echo '<label>' . elgg_echo('email') . '</label>';
	echo '<div class="mbm">' . $entity->email . '</div>';
}

foreach ($questions as $question) {
	$answer = $question->getAnswerFromUser($entity->guid);

	echo '<label>' . $question->title . '</label>';
	echo '<div class="mbm">' . $answer->value . '</div>';
}
