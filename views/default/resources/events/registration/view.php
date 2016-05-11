<?php
$key = elgg_extract('k', $vars);
$guid = (int) elgg_extract('guid', $vars);
$user_guid = (int) elgg_extract('u_g', $vars);

elgg_entity_gatekeeper($guid, 'object', Event::SUBTYPE);
$event = get_entity($guid);

$output = '';
$title_text = elgg_echo('event_manager:registration:registrationto') . " '{$event->title}'";
elgg_push_breadcrumb($event->title, $event->getURL());

if (!empty($key)) {
	// registration of a non logged in user
	
	$entity = get_entity($user_guid);
	if (empty($entity)) {
		forward('events');
	}
	
	if (!elgg_build_hmac([$event->time_created, $user_guid])->matchesToken($key)) {
		forward('events');
	}

	$old_ia = elgg_set_ignore_access(true);

	$output .= elgg_view('event_manager/event/pdf', ['entity' => $event]);
	$output .= elgg_view('event_manager/registration/user_data', [
		'event' => $event,
		'entity' => $entity,
	]);

	if ($event->with_program) {
		$output .= $event->getProgramData($user_guid);
	}

	elgg_set_ignore_access($old_ia);

} else {
	gatekeeper();
	
	if (!$event->canEdit() && ($user_guid !== elgg_get_logged_in_user_guid())) {
		forward($event->getURL());
	}
	
	$output .= elgg_view('event_manager/event/pdf', ['entity' => $event]);
	$output .= elgg_view('event_manager/registration/user_data', [
		'event' => $event,
		'entity' => elgg_get_logged_in_user_entity(),
	]);

	if ($event->with_program) {
		$output .= $event->getProgramData($user_guid);
	}

	if ($user_guid == elgg_get_logged_in_user_guid()) {
		elgg_register_menu_item('title', \ElggMenuItem::factory([
			'name' => 'edityourregistration',
			'text' => elgg_echo('event_manager:registration:edityourregistration'),
			'link_class' => 'elgg-button elgg-button-action',
			'href' => 'events/event/register/' . $event->getGUID() . '/event_attending',
		]));
	}
}

elgg_register_menu_item('title', \ElggMenuItem::factory([
	'name' => 'save_to_pdf',
	'text' => elgg_echo('event_manager:registration:view:savetopdf'),
	'link_class' => 'elgg-button elgg-button-action',
	'href' => 'action/event_manager/registration/pdf?k=' . elgg_build_hmac([$event->time_created, $user_guid])->getToken() . '&guid=' . $guid . '&u_g=' . $user_guid,
	'is_action' => true,
]));

$body = elgg_view_layout('content', [
	'filter' => '',
	'content' => $output,
	'title' => $title_text,
]);

echo elgg_view_page($title_text, $body);
