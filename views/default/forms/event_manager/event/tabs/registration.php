<?php
/**
 * Form fields for entering registration settings
 */

$event = elgg_extract('entity', $vars);

$organizer_label = elgg_echo('event_manager:edit:form:organizer');
$organizer_input = elgg_view('input/text', array(
	'name' => 'organizer',
	'value' => $vars["organizer"],
));

$organizer_rsvp_label = elgg_echo('event_manager:edit:form:registration_options');

$organizer_rsvp_input = '';
if (!$event) {
	$organizer_rsvp = elgg_view('input/checkboxes', array(
		'name' => 'organizer_rsvp',
		'id' => 'organizer_rsvp',
		'value' => $vars["organizer_rsvp"],
		'options' => array(
			elgg_echo('event_manager:edit:form:organizer_rsvp') => '1',
		)
	));
}

$with_program = elgg_view('input/checkboxes', array(
	'name' => 'with_program',
	'id' => 'with_program',
	'value' => $vars["with_program"],
	'options' => array(
		elgg_echo('event_manager:edit:form:with_program') => '1',
	)
));

$registration_needed = elgg_view('input/checkboxes', array(
	'name' => 'registration_needed',
	'value' => $vars["registration_needed"],
	'options' => array(
		elgg_echo('event_manager:edit:form:registration_needed') => '1',
	)
));

$waiting_list_enabled = elgg_view('input/checkboxes', array(
	'name' => 'waiting_list_enabled',
	'value' => $vars["waiting_list_enabled"],
	'options' => array(
		elgg_echo('event_manager:edit:form:waiting_list') => '1'
	)
));

$register_nologin = '';
if (!elgg_get_config("walled_garden")) {
	$register_nologin = elgg_view('input/checkboxes', array(
		'name' => 'register_nologin',
		'value' => $vars["register_nologin"],
		'options' => array(
			elgg_echo('event_manager:edit:form:register_nologin') => '1',
		)
	));
}

$end_registration_day_label = elgg_echo('event_manager:edit:form:endregistration_day');
$end_registration_day_input = elgg_view('input/date', array(
	'name' => 'endregistration_day',
	'id' => 'endregistration_day',
	'value' => (($vars["endregistration_day"] != 0) ? date('Y-m-d', $vars["endregistration_day"]) : ''),
));

$rsvp_options_label = elgg_echo('event_manager:edit:form:rsvp_options');

$registration_ended = elgg_view('input/checkboxes', array(
	'name' => 'registration_ended',
	'value' => $vars["registration_ended"],
	'options' => array(
		elgg_echo('event_manager:edit:form:registration_ended') => '1',
	)
));

$event_interested = elgg_view('input/checkboxes', array(
	'name' => 'event_interested',
	'id' => 'event_interested',
	'value' => $vars["event_interested"],
	'options' => array(
		elgg_echo('event_manager:event:relationship:event_interested') => '1',
	)
));

$event_presenting = elgg_view('input/checkboxes', array(
	'name' => 'event_presenting',
	'id' => 'event_presenting',
	'value' => $vars["event_presenting"],
	'options' => array(
		elgg_echo('event_manager:event:relationship:event_presenting') => '1',
	)
));

$event_exhibiting = elgg_view('input/checkboxes', array(
	'name' => 'event_exhibiting',
	'id' => 'event_exhibiting',
	'value' => $vars["event_exhibiting"],
	'options' => array(
		elgg_echo('event_manager:event:relationship:event_exhibiting') => '1',
	)
));

$event_organizing = elgg_view('input/checkboxes', array(
	'name' => 'event_organizing',
	'id' => 'event_organizing',
	'value' => $vars["event_organizing"],
	'options' => array(
		elgg_echo('event_manager:event:relationship:event_organizing') => '1',
	)
));

$fee_label = elgg_echo('event_manager:edit:form:fee');
$fee_input = elgg_view('input/text', array(
	'name' => 'fee',
	'value' => $vars["fee"]
));

$max_attendees_label = elgg_echo('event_manager:edit:form:max_attendees');
$max_attendees_input = elgg_view('input/text', array(
	'name' => 'max_attendees',
	'value' => $vars["max_attendees"]
));

echo <<<HTML
	<div>
		<label>$fee_label</label>
		$fee_input
	</div>
	<div>
		<label>$max_attendees_label</label>
		$max_attendees_input
	</div>
	<div>
		<label>$organizer_label</label>
		$organizer_input
	</div>
	<div>
		<label>$organizer_rsvp_label</label>
		$organizer_rsvp_input
		$with_program
		$registration_needed
		$waiting_list_enabled
		$register_nologin
	</div>
	<div>
		<label>$end_registration_day_label</label>
		$end_registration_day_input
	</div>
	<div>
		<label>$rsvp_options_label</label>
		$registration_ended
		$event_interested
		$event_presenting
		$event_exhibiting
		$event_organizing
	</div>
HTML;

