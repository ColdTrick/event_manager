<?php

$organizer = elgg_extract('organizer', $vars);
$contact_details = elgg_extract('contact_details', $vars);
$website = elgg_extract('website', $vars);
$organizer_guids = elgg_extract('organizer_guids', $vars);
$contact_guids = elgg_extract('contact_guids', $vars);

$collapsed = true;
if (!empty($organizer) || !empty($organizer_guids) || !empty($contact_guids) || !empty($contact_details) || !empty($website)) {
	$collapsed = false;
}

$output = '';

$output .= '<div class="clearfix">';
$output .= '<div class="elgg-col elgg-col-4of5">';
$output .= elgg_view_input('text', [
	'label' => elgg_echo('event_manager:edit:form:organizer'),
	'name' => 'organizer',
	'value' => $organizer,
]);
$output .= '</div>';
$output .= '<div class="elgg-col elgg-col-1of5"><div class="mlm mtl float-alt">';
$output .= elgg_format_element('span', ['class' => 'phm'], elgg_echo('event_manager:edit:form:users:or'));
$output .= elgg_view('input/button', [
	'value' => elgg_echo('event_manager:edit:form:users:add'),
	'class' => 'elgg-button-action',
	'rel' => 'toggle',
	'data-toggle-selector' => '.event-manager-contact-organizer-guids',
]);
$output .= '</div>';
$output .= '</div>';
$output .= '</div>';

$field_classes = ['event-manager-contact-organizer-guids', 'event-manager-contact-userpicker'];
if (empty($organizer_guids)) {
	$field_classes[] = 'hidden';
}
$output .= elgg_view_input('userpicker', [
	'name' => 'organizer_guids',
	'field_class' => $field_classes,
	'label' => elgg_echo('event_manager:edit:form:organizer_guids'),
	'values' => $organizer_guids,
]);

$output .= '<div class="clearfix">';
$output .= '<div class="elgg-col elgg-col-4of5">';
$output .= elgg_view_input('text', [
	'label' => elgg_echo('event_manager:edit:form:contact_details'),
	'name' => 'contact_details',
	'value' => $contact_details,
]);
$output .= '</div>';
$output .= '<div class="elgg-col elgg-col-1of5"><div class="mlm mtl float-alt">';
$output .= elgg_format_element('span', ['class' => 'phm'], elgg_echo('event_manager:edit:form:users:or'));
$output .= elgg_view('input/button', [
	'value' => elgg_echo('event_manager:edit:form:users:add'),
	'class' => 'elgg-button-action',
	'rel' => 'toggle',
	'data-toggle-selector' => '.event-manager-contact-contact-guids',
]);
$output .= '</div>';
$output .= '</div>';
$output .= '</div>';

$field_classes = ['event-manager-contact-contact-guids', 'event-manager-contact-userpicker'];
if (empty($contact_guids)) {
	$field_classes[] = 'hidden';
}
$output .= elgg_view_input('userpicker', [
	'name' => 'contact_guids',
	'field_class' => $field_classes,
	'label' => elgg_echo('event_manager:edit:form:contact_guids'),
	'values' => $contact_guids,
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