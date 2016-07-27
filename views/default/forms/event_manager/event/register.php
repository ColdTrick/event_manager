<?php

$event = elgg_extract('entity', $vars);
$register_type = elgg_extract('register_type', $vars, 'register');

$show_required = false;

$form_body = '';

if (!elgg_is_logged_in()) {
	$form_body .= elgg_view('event_manager/registration/non_loggedin');
	$show_required = true;
}

$registration_form = $event->getRegistrationFormQuestions();
if ($registration_form) {
	if ($register_type == 'waitinglist') {
		$form_body .= '<p>' . elgg_echo('event_manager:event:rsvp:waiting_list:message') . '</p><br />';
	}

	foreach ($registration_form as $question) {

		$value = elgg_get_sticky_value('event_register', 'question_' . $question->getGUID());

		if ($value == null) {
			if (elgg_is_logged_in()) {
				$answer = $question->getAnswerFromUser();
				if ($answer) {
					$value = $answer->value;
				}
			}
		}

		$form_body .= elgg_view('event_manager/registration/question', [
			'entity' => $question,
			'value' => $value,
		]);

		if ($question->required) {
			$show_required = true;
		}
	}
}

if ($show_required) {
	$form_body .= elgg_format_element('div', ['class' => 'elgg-subtext'], elgg_echo('event_manager:registration:required_fields:info'));
}

if (!empty($form_body)) {
	$form_body = elgg_view_module('info', '', $form_body, ['id' => 'event_manager_registration_form_fields']);
}

if ($event->with_program) {
	$form_body .= $event->getProgramData(elgg_get_logged_in_user_guid(), true, $register_type);
}

if (!$form_body) {
	return;
}

$form_body .= elgg_view('input/hidden', ['name' => 'event_guid', 'value' => $event->getGUID()]);
$form_body .= elgg_view('input/hidden', ['name' => 'register_type', 'value' => $register_type]);

if ($register_type == 'register') {
	$form_body .= elgg_view('input/hidden', ['name' => 'relation', 'value' => EVENT_MANAGER_RELATION_ATTENDING]);
} elseif ($register_type == 'waitinglist') {
	$form_body .= elgg_view('input/hidden', ['name' => 'relation', 'value' => EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST]);
}
	
$form_body .= elgg_view('input/submit', ['value' => elgg_echo('register')]);

echo $form_body;
