<?php

$event = elgg_extract('entity', $vars);
$registration = elgg_extract('registration', $vars);
$code = elgg_extract('code', $vars);

echo elgg_view('output/longtext', [
	'value' => elgg_echo('event_manager:unsubscribe_confirm:description', [$registration->getDisplayName(), $event->getDisplayName()]),
]);

echo elgg_view('input/hidden', ['name' => 'registration', 'value' => $registration->guid]);
echo elgg_view('input/hidden', ['name' => 'event', 'value' => $event->guid]);
echo elgg_view('input/hidden', ['name' => 'code', 'value' => $code]);

$footer = elgg_view('input/submit', ['value' => elgg_echo('confirm')]);
elgg_set_form_footer($footer);
