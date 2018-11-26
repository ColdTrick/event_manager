<?php

$event = elgg_extract('event', $vars);
$user = elgg_extract('user', $vars);
$code = elgg_extract('code', $vars);

echo elgg_view('output/longtext', [
	'value' => elgg_echo('event_manager:registration:confirm:description', [$user->getDisplayName(), $event->getDisplayName()]),
]);

echo elgg_view('input/hidden', ['name' => 'event_guid', 'value' => $event->guid]);
echo elgg_view('input/hidden', ['name' => 'user_guid', 'value' => $user->guid]);
echo elgg_view('input/hidden', ['name' => 'code', 'value' => $code]);

$footer = elgg_view('input/submit', ['value' => elgg_echo('confirm')]);
$footer .= elgg_view('output/url', [
	'text' => elgg_echo('delete'),
	'confirm' => elgg_echo('event_manager:registration:confirm:delete'),
	'href' => elgg_generate_action_url('event_manager/event/rsvp', [
		'guid' => $event->guid,
		'user' => $user->guid,
		'code' => $code,
		'type' => EVENT_MANAGER_RELATION_UNDO,
		'forward_url' => $event->getURL(),
	]),
	'class' => 'float-alt elgg-button elgg-button-delete',
]);
elgg_set_form_footer($footer);
