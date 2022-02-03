<?php

$guid = (int) get_input('guid');
$post = $_POST;
$registrationFields = [];

$registration = get_entity($guid);
if (!$registration instanceof EventRegistration) {
	return elgg_echo('actionunauthorized');
}

foreach ($post as $key => $value) {
	if (substr($key, 0, 8) !== 'question') {
		continue;
	}

	$questionId = substr($key, 8, strlen($key));
	$registrationFields[] = $questionId . '|' . $value;
}

$events = $registration->getEntitiesFromRelationship([
	'relationship' => 'event_user_registered',
	'inverse_relationship' => true,
]);

$event = $events[0];

$registration->clearAnnotations('answer');

foreach ($registrationFields as $answer) {
	$registration->annotate('answer', $answer, $event->access_id);
}

return elgg_ok_response('', elgg_echo('event_manager:action:event:edit:ok'), $event->getURL());
