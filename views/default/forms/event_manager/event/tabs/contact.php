<?php

$organizer = elgg_extract('organizer', $vars);
$contact_details = elgg_extract('contact_details', $vars);
$website = elgg_extract('website', $vars);
$organizer_guids = elgg_extract('organizer_guids', $vars);
$contact_guids = elgg_extract('contact_guids', $vars);

$output = '';

$output .= '<div class="clearfix">';
$output .= '<div class="elgg-col elgg-col-3of5">';
$output .= elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('event_manager:edit:form:organizer'),
	'#help' => elgg_echo('event_manager:edit:form:organizer:help'),
	'name' => 'organizer',
	'value' => $organizer,
]);
$output .= '</div>';
$output .= '<div class="elgg-col elgg-col-2of5"><div class="mlm mtl float-alt">';
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
$output .= elgg_view_field([
	'#type' => 'userpicker',
	'#label' => elgg_echo('event_manager:edit:form:organizer_guids'),
	'#class' => $field_classes,
	'name' => 'organizer_guids',
	'values' => $organizer_guids,
]);

$output .= '<div class="clearfix">';
$output .= '<div class="elgg-col elgg-col-3of5">';
$output .= elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('event_manager:edit:form:contact_details'),
	'#help' => elgg_echo('event_manager:edit:form:contact_details:help'),
	'name' => 'contact_details',
	'value' => $contact_details,
]);
$output .= '</div>';
$output .= '<div class="elgg-col elgg-col-2of5"><div class="mlm mtl float-alt">';
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
$output .= elgg_view_field([
	'#type' => 'userpicker',
	'#label' => elgg_echo('event_manager:edit:form:contact_guids'),
	'#class' => $field_classes,
	'name' => 'contact_guids',
	'values' => $contact_guids,
]);

$output .= elgg_view_field([
	'#type' => 'url',
	'#label' => elgg_echo('event_manager:edit:form:website'),
	'#help' => elgg_echo('event_manager:edit:form:website:help'),
	'name' => 'website',
	'value' => $website,
]);

echo $output;
