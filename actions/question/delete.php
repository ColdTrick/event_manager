<?php

$question_guid = (int) get_input('guid');

elgg_entity_gatekeeper($question_guid, 'object', EventRegistrationQuestion::SUBTYPE);
$question = get_entity($question_guid);

if (!$question->canEdit()) {
	register_error(elgg_echo('actionunauthorized'));
	forward(REFERER);
}

$question->delete();
