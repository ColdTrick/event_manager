<?php

$question_guid = (int) get_input("guid");

$question = get_entity($question_guid);

if (!($question instanceof EventRegistrationQuestion) || !$question->canEdit()) {
	return;
}

$question->delete();
