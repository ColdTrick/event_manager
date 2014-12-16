<?php
/**
 * Form fields for event settings
 */

$options_label = elgg_echo('event_manager:edit:form:options');

$comments_on = elgg_view('input/checkboxes', array(
	'name' => 'comments_on',
	'value' => $vars["comments_on"],
	'options' => array(
		elgg_echo('event_manager:edit:form:comments_on') => '1',
	)
));

$notify_onsignup = elgg_view('input/checkboxes', array(
	'name' => 'notify_onsignup',
	'value' => $vars["notify_onsignup"],
	'options' => array(
		elgg_echo('event_manager:edit:form:notify_onsignup') => '1',
	)
));

$show_attendees = elgg_view('input/checkboxes', array(
	'name' => 'show_attendees',
	'value' => $vars["show_attendees"],
	'options' => array(
		elgg_echo('event_manager:edit:form:show_attendees') => '1',
	)
));

$hide_owner_block = elgg_view('input/checkboxes', array(
	'name' => 'hide_owner_block',
	'value' => $vars["hide_owner_block"],
	'options' => array(
		elgg_echo('event_manager:edit:form:hide_owner_block') => '1',
	)
));

$registration_completed_label = elgg_echo('event_manager:edit:form:registration_completed');
$registration_completed_input = elgg_view('input/longtext', array(
	'name' => 'registration_completed',
	'value' => $vars["registration_completed"],
));
$registration_completed_desc = elgg_echo("event_manager:edit:form:registration_completed:description");

echo <<<HTML
	<div>
		<label>$options_label</label>
		$comments_on
		$notify_onsignup
		$show_attendees
		$hide_owner_block
	</div>
	<div>
		<label>$registration_completed_label</label>
		$registration_completed_input
		<span class="elgg-text-help">$registration_completed_desc</span>
	</div>
HTML;
