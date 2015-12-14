<?php
$key = get_input('k');
$guid = (int) get_input("guid");
$user_guid = (int) get_input('u_g', elgg_get_logged_in_user_guid());
$event = null;

if ($guid && ($entity = get_entity($guid))) {
	if ($entity instanceof Event) {
		$event = $entity;
	}
}

$output = "";
if ($event) {
	elgg_register_menu_item("title", ElggMenuItem::factory([
		"name" => "save_to_pdf",
		"text" => elgg_echo('event_manager:registration:view:savetopdf'),
		"link_class" => "elgg-button elgg-button-action",
		"href" => "action/event_manager/registration/pdf?k=" . elgg_build_hmac([$event->time_created, $user_guid])->getToken() . "&guid=" . $guid . "&u_g=" . $user_guid,
		"is_action" => true
	]));
}

if ($event && !empty($key)) {
	$tempKey = elgg_build_hmac([$event->time_created, $user_guid])->getToken();
	$entity = get_entity($user_guid);
	if (($tempKey == $key) && $entity) {

		$title_text = elgg_echo('event_manager:registration:registrationto') . " '" . $event->title . "'";

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

		elgg_push_breadcrumb($event->title, $event->getURL());
		elgg_push_breadcrumb($title_text);

		$body = elgg_view_layout('content', array(
			'filter' => '',
			'content' => $output,
			'title' => $title_text,
		));

		echo elgg_view_page($title_text, $body);

	} else {
		forward("events");
	}
} else {
	gatekeeper();

	if ($event) {
		if ($event->canEdit() || ($user_guid == elgg_get_logged_in_user_guid())) {
			$title_text = elgg_echo('event_manager:registration:registrationto') . " '" . $event->title . "'";

			$output .= elgg_view('event_manager/event/pdf', ['entity' => $event]);
			$output .= elgg_view('event_manager/registration/user_data', [
				'event' => $event,
				'entity' => elgg_get_logged_in_user_entity(),
			]);

			if ($event->with_program) {
				$output .= $event->getProgramData($user_guid);
			}

			if ($user_guid == elgg_get_logged_in_user_guid()) {
				$output .= '<br /><a class="mlm" href="' . elgg_get_site_url() . 'events/event/register/' . $event->getGUID() . '/event_attending">' . elgg_echo('event_manager:registration:edityourregistration') . '</a>';
			}

			elgg_push_breadcrumb($event->title, $event->getURL());
			elgg_push_breadcrumb($title_text);

			$body = elgg_view_layout('content', array(
				'filter' => '',
				'content' => $output,
				'title' => $title_text,
			));

			echo elgg_view_page($title_text, $body);
		} else {
			forward($event->getURL());
		}
	} else {
		register_error(elgg_echo("InvalidParameterException:GUIDNotFound", array($guid)));
		forward(REFERER);
	}
}
