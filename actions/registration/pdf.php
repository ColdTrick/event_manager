<?php

elgg_load_library('dompdf');

$key = get_input('k');
$guid = (int) get_input('guid');
$user_guid = (int) get_input('u_g', elgg_get_logged_in_user_guid());
$event = null;

if ($guid && ($entity = get_entity($guid))) {
	if ($entity instanceof Event) {
		$event = $entity;
	}
}

if (!$event || empty($key)) {
	forward('events');
}

$tempKey = elgg_build_hmac([$event->time_created, $user_guid])->getToken();

$entity = get_entity($user_guid);
if (empty($entity) || ($tempKey !== $key)) {
	forward('events');
}

$html = elgg_view_title(elgg_echo('event_manager:registration:yourregistration'));

$html .= elgg_view('event_manager/event/pdf', ['entity' => $event]);

$old_ia = elgg_set_ignore_access(true);

$html .= elgg_view('event_manager/registration/user_data', [
	'event' => $event,
	'entity' => $entity,
	'show_title' => true,
]);

if ($event->with_program) {
	elgg_push_context('programmailview');

	$html .= elgg_view_module('main', '', elgg_view('event_manager/program/pdf', ['entity' => $event, 'user_guid' => $user_guid]));

	elgg_pop_context();
}

elgg_set_ignore_access($old_ia);

$dompdf = new DOMPDF();
$dompdf->set_paper('A4');
$dompdf->load_html($html);
$dompdf->render();
$dompdf->stream('registration.pdf');

exit;
