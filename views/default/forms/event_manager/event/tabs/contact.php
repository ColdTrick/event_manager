<?php

$organizer = elgg_extract('organizer', $vars);
$contact_details = elgg_extract('contact_details', $vars);
$website = elgg_extract('website', $vars);

$collapsed = true;
if (!empty($organizer) || !empty($contact_details) || !empty($website)) {
	$collapsed = false;
}

$output = elgg_view_input('text', [
	'label' => elgg_echo('event_manager:edit:form:organizer'),
	'name' => 'organizer',
	'value' => $organizer,
]);

$output .= elgg_view_input('text', [
	'label' => elgg_echo('event_manager:edit:form:contact_details'),
	'name' => 'contact_details',
	'value' => $contact_details,
]);

$output .= elgg_view_input('url', [
	'label' => elgg_echo('event_manager:edit:form:website'),
	'name' => 'website',
	'value' => $website,
]);

if (!$collapsed) {
	echo $output;
	return;
}

$toggle_button = elgg_view('input/button', [
	'class' => 'elgg-button-action',
	'value' => elgg_echo('event_manager:edit:form:tabs:contact:toggle'),
	'rel' => 'toggle',
	'data-toggle-selector' => '.event-manager-edit-contact-toggle',
]);

echo elgg_format_element('div', [
	'class' => 'event-manager-edit-contact-toggle center',
	'data-toggle-slide' => 0,
], $toggle_button);

echo elgg_format_element('div', [
	'class' => 'hidden event-manager-edit-contact-toggle',
	'data-toggle-slide' => 0,
], $output);