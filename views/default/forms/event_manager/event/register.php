<?php 

$event = elgg_extract("entity", $vars);
$register_type = elgg_extract("register_type", $vars, "register");

$show_required = false;

$form_body = "";

if (!elgg_is_logged_in()) {
	$form_body .= elgg_view("event_manager/registration/non_loggedin");
	$show_required = true;
}

$registration_form = $event->getRegistrationFormQuestions();
if ($registration_form) {
	if ($register_type == "waitinglist") {
		$form_body .= "<p>" . elgg_echo("event_manager:event:rsvp:waiting_list:message") . "</p><br />";
	}

	$form_body .= "<ul>";
		
	foreach ($registration_form as $question) {
		$value = null;
		if (array_key_exists("registerevent_values", $_SESSION) && is_array($_SESSION["registerevent_values"])) {
			$value = elgg_extract("question_" . $question->getGUID(), $_SESSION["registerevent_values"]);
		}

		if ($value == null) {
			if (elgg_is_logged_in()) {
				$answer = $question->getAnswerFromUser();
				if ($answer) {
					$value = $answer->value;
				}
			}
		}

		$form_body .= elgg_view("event_manager/registration/question", array("entity" => $question, "register" => true, "value" => $value));

		if ($question->required) {
			$show_required = true;
		}
	}
		
	$form_body .= "</ul>";
}

if ($show_required) {
	$form_body .= "<div class='elgg-subtext'>" . elgg_echo("event_manager:registration:required_fields:info") . "</div>";
}

if (!empty($form_body)) {
	$form_body = elgg_view_module("info", "", $form_body, array("id" => "event_manager_registration_form_fields"));
}

if ($event->with_program) {
	$form_body .= $event->getProgramData(elgg_get_logged_in_user_guid(), true, $register_type);
}

if ($form_body) {
	$form_body .= elgg_view("input/hidden", array("name" => "event_guid", "value" => $event->getGUID()));
	$form_body .= elgg_view("input/hidden", array("name" => "register_type", "value" => $register_type));
	
	if ($register_type == "register") {
		$form_body .= elgg_view("input/hidden", array("name" => "relation", "value" => EVENT_MANAGER_RELATION_ATTENDING));
	} elseif ($register_type == "waitinglist") {
		$form_body .= elgg_view("input/hidden", array("name" => "relation", "value" => EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST));
	}
		
	$form_body .= elgg_view("input/submit", array("value" => elgg_echo("register")));
}

echo $form_body;
