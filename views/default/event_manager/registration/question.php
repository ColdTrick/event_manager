<?php 

$question = $vars["entity"];
$value = $vars["value"];
$register = elgg_extract("register", $vars, false);
$actions = "";

if (!empty($question) && ($question instanceof EventRegistrationQuestion)) {
	if ($question->canEdit() && !$register) {
		$edit_question = elgg_view("output/url", array(
			"href" => "javascript:void(0);",
			"text" => elgg_view_icon("settings-alt"),
			"class" => "event_manager_questions_edit mlm",
			"title" => elgg_echo("edit"),
			"rel" => $question->getGUID(),
		));
		
		$delete_question = elgg_view("output/url", array(
			"href" => "javascript:void(0);",
			"text" => elgg_view_icon("delete"),
			"class" => "event_manager_questions_delete",
			"title" => elgg_echo("delete"),
			"rel" => $question->getGUID(),
		));
		
		$actions = $edit_question . " " . $delete_question;
	}
	
	$fieldtypes = array(
		"Textfield" => "text",
		"Textarea" => "plaintext",
		"Dropdown" => "dropdown",
		"Radiobutton" => "radio"
	);
	
	$result = "";
	
	if (array_key_exists($question->fieldtype, $fieldtypes)) {			
		$field_options = $question->getOptions();
		
		$required = "";
		$required_class = "";
		
		if ($question->required) {
			$required = " *";
			$required_class = "required";
		}
		
		if ($question->fieldtype == "Checkbox") {
			$field_options = array($question->title . $required => "1");
			
			$result = $actions . elgg_view("input/" . $fieldtypes[$question->fieldtype], array(
				"name" => "question_" . $question->getGUID(), 
				"value" => $value, 
				"options" => $field_options, 
				"class" => $required_class
			));
		} else {
			$result = "";
			if (!$register) {
				$result = elgg_view_icon("cursor-drag-arrow", "mrm");
			}
			$result .= "<label>" . $question->title . $required . "</label>" . $actions . "<br />" . elgg_view("input/" . $fieldtypes[$question->fieldtype], array("name" => "question_" . $question->getGUID(), "value" => $value, "options" => $field_options, "class" => $required_class));
		}
	}
	
	$class = "";
	if (!$register) {
		$class = " class='elgg-module-popup'";
	}
	
	echo "<li" . $class . " id='question_" . $question->getGUID() . "'>" . $result . "</li>";
}
