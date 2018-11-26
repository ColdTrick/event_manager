<?php

elgg_make_sticky_form('event_unsubscribe');

$guid = (int) get_input('guid');
$email = get_input('email');

elgg_entity_gatekeeper($guid, 'object', Event::SUBTYPE);
$entity = get_entity($guid);

if (!empty($email) && !is_email_address($email)) {
	return elgg_error_response(elgg_echo('registration:notemail'));
}
			
// try to find a registration
$registrations = elgg_get_entities([
	'type' => 'object',
	'subtype' => EventRegistration::SUBTYPE,
	'owner_guid' => $entity->guid,
	'limit' => 1,
	'metadata_name_value_pairs' => [
		'name' => 'email',
		'value' => $email,
		'case_sensitive' => false,
	],
]);

if (empty($registrations)) {
	return elgg_error_response(elgg_echo('event_manager:action:unsubscribe:error:no_registration'));
}

$registration = $registrations[0];

$unsubscribe_link = elgg_generate_url('default:object:event:unsubscribe:confirm', [
	'guid' => $registration->guid,
	'code' => event_manager_create_unsubscribe_code($registration, $entity),
]);

// make a message with further instructions
$subject = elgg_echo('event_manager:unsubscribe:confirm:subject', [$entity->getDisplayName()]);
$message = elgg_echo('event_manager:unsubscribe:confirm:message', [
	$registration->getDisplayName(),
	$entity->getDisplayName(),
	$entity->getURL(),
	$unsubscribe_link,
]);

$email_sent = elgg_send_email(\Elgg\Email::factory([
	'to' => $registration,
	'subject' => $subject,
	'body' => $message,
]));

if (!$email_sent) {
	return elgg_error_response(elgg_echo('event_manager:action:unsubscribe:error:mail'));
}

elgg_clear_sticky_form('event_unsubscribe');

return elgg_ok_response('', elgg_echo('event_manager:action:unsubscribe:success'), $entity->getURL());
