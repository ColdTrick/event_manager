<?php
use Elgg\Exceptions\HttpException;

$key = elgg_extract('k', $vars);
$guid = (int) elgg_extract('guid', $vars);
$user_guid = (int) elgg_extract('u_g', $vars, elgg_get_logged_in_user_guid());

elgg_entity_gatekeeper($guid, 'object', \Event::SUBTYPE);

/* @var $event \Event */
$event = get_entity($guid);

$output = '';
$title_text = elgg_echo('event_manager:registration:registrationto') . " '{$event->getDisplayName()}'";

elgg_push_entity_breadcrumbs($event);

if (!empty($key)) {
	// registration of a non logged in user
	
	$entity = get_entity($user_guid);
	if (empty($entity)) {
		$exception = new HttpException();
		$exception->setRedirectUrl(elgg_generate_url('default:object:event'));
		throw $exception;
	}
	
	if (!elgg_build_hmac([$event->time_created, $user_guid])->matchesToken($key)) {
		$exception = new HttpException();
		$exception->setRedirectUrl(elgg_generate_url('default:object:event'));
		throw $exception;
	}

	$output .= elgg_call(ELGG_IGNORE_ACCESS, function() use ($entity, $event) {
		
		$result = elgg_view('event_manager/event/pdf', ['entity' => $event]);
		$result .= elgg_view('event_manager/registration/user_data', [
			'event' => $event,
			'entity' => $entity,
		]);
	
		if ($event->with_program) {
			$result .= $event->getProgramData($entity->guid);
		}
		
		return $result;
	});
} else {
	elgg_gatekeeper();
	
	if (!$event->canEdit() && ($user_guid !== elgg_get_logged_in_user_guid())) {
		$exception = new HttpException();
		$exception->setRedirectUrl(elgg_generate_url('default:object:event'));
		throw $exception;
	}
	
	$output .= elgg_view('event_manager/event/pdf', ['entity' => $event]);
	$output .= elgg_view('event_manager/registration/user_data', [
		'event' => $event,
		'entity' => elgg_get_logged_in_user_entity(),
	]);

	if ($event->with_program) {
		$output .= $event->getProgramData($user_guid);
	}

	if ($user_guid === elgg_get_logged_in_user_guid()) {
		elgg_register_menu_item('title', [
			'name' => 'edityourregistration',
			'icon' => 'edit',
			'text' => elgg_echo('event_manager:registration:edityourregistration'),
			'link_class' => 'elgg-button elgg-button-action',
			'href' => elgg_generate_url('default:object:event:register', [
				'guid' => $event->guid,
			]),
		]);
	}
}

elgg_register_menu_item('title', [
	'name' => 'save_to_pdf',
	'icon' => 'download',
	'text' => elgg_echo('event_manager:registration:view:savetopdf'),
	'link_class' => 'elgg-button elgg-button-action',
	'href' => elgg_generate_action_url('event_manager/registration/pdf', [
		'k' => elgg_build_hmac([$event->time_created, $user_guid])->getToken(),
		'guid' => $guid,
		'u_g' => $user_guid,
	]),
]);

echo elgg_view_page($title_text, [
	'content' => $output,
	'filter' => false,
]);
