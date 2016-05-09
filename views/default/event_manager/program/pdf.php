<?php

$event = elgg_extract('entity', $vars);
$user_guid = elgg_extract('user_guid', $vars);

if (!($event instanceof Event)) {
	return;
}

if (!$event->with_program) {
	return;
}

$eventDays = $event->getEventDays();
if (empty($eventDays)) {
	return;
}

echo elgg_format_element('h3', [], elgg_echo('event_manager:event:program'));

foreach ($eventDays as $day) {
	echo elgg_view('event_manager/program/pdf/day', [
		'entity' => $day,
		'selected' => $selected,
		'user_guid' => $user_guid,
	]);
}
