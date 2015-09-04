<?php

$guid_order = get_input('question');

if (empty($guid_order)) {
	register_error(elgg_echo('event_manager:registrationform:fieldorder:error'));
	return;
}

foreach ($guid_order as $order => $question_guid) {
	$question = get_entity($question_guid);
	
	if (!($question instanceof EventRegistrationQuestion)) {
		continue;
	}
	
	if (!$question->canEdit()) {
		continue;
	}
	
	$question->order = $order;
}
