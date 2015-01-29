<?php 

$registration = $vars["entity"];
$owner = $registration->getOwnerEntity();

$output .= "<div class='event_manager_registration_info'>";
$output .= "<a class='user' href='" . $owner->getURL() . "'>" . $owner->name . "</a> - " . elgg_view_friendly_time($registration->time_created) . "<br />";
$output .= "</div>";

$answers = $registration->getAnnotations("answer", 100, 0, "a.id asc");

if ($answers) {
	foreach ($answers as $answer) {
		$answerExplode = explode("|", $answer->value);
		$answerId = $answerExplode[0]; 
		$answerValue = $answerExplode[1];
		
		$question = elgg_get_annotation_from_id($answerId);
		
		$output .= "<br /><h3>" . $question->value . "</h3>";
		$output .= $answerValue . "<br />";
	}
}

echo elgg_view_module("main", "", $output);
