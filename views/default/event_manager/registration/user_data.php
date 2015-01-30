<?php

$entity = elgg_extract("entity", $vars);
$questions = elgg_extract("questions", $vars);
$show_title = elgg_extract("show_title", $vars, false);

if (empty($questions)) {
	return;
}

$output = "";
if ($show_title) {
	$output .= "<h3>" . elgg_echo("event_manager:registration:view:information") . "</h3>";
}

$output .= "<table>";

if (($entity->guid != elgg_get_logged_in_user_guid()) && !($entity instanceof ElggUser)) {
	$output .= "<tr><td><label>" . elgg_echo("user:name:label") . ":</label>&nbsp;</td><td>{$entity->name}</td></tr>";
	$output .= "<tr><td><label>" . elgg_echo("email") . ":</label>&nbsp;</td><td>{$entity->email}</td></tr>";
}

foreach ($questions as $question) {
	$answer = $question->getAnswerFromUser($entity->guid);

	$output .= "<tr><td><label>{$question->title}:</label>&nbsp;</td><td>{$answer->value}</td></tr>";
}

$output .= "</table>";

echo elgg_view_module("main", "", $output);