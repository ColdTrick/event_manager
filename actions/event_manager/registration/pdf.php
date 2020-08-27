<?php

use Dompdf\Dompdf;

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
	return elgg_redirect_response(elgg_generate_url('default:object:event'));
}

$tempKey = elgg_build_hmac([$event->time_created, $user_guid])->getToken();

$entity = get_entity($user_guid);
if (empty($entity) || ($tempKey !== $key)) {
	return elgg_redirect_response(elgg_generate_url('default:object:event'));
}

$html = elgg_view_title(elgg_echo('event_manager:registration:yourregistration'));

$html .= elgg_view('event_manager/event/pdf', ['entity' => $event]);

$html .= elgg_call(ELGG_IGNORE_ACCESS, function () use ($event, $entity) {
	$output = elgg_view('event_manager/registration/user_data', [
		'event' => $event,
		'entity' => $entity,
		'show_title' => true,
	]);
	
	if ($event->with_program) {
		elgg_push_context('programmailview');
	
		$output = elgg_view_module('main', '', elgg_view('event_manager/program/pdf', ['entity' => $event, 'user_guid' => $entity->guid]));
	
		elgg_pop_context();
	}
	
	return $output;
});

$dompdf = new Dompdf();
$dompdf->setPaper('A4');
$dompdf->loadHtml($html);
$dompdf->render();
$dompdf->stream('registration.pdf');

exit;
