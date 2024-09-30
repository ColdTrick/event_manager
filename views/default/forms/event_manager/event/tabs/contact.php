<?php

elgg_import_esm('elgg/toggle');

$organizer = elgg_extract('organizer', $vars);
$contact_details = elgg_extract('contact_details', $vars);
$website = elgg_extract('website', $vars);
$organizer_guids = elgg_extract('organizer_guids', $vars);
$contact_guids = elgg_extract('contact_guids', $vars);

$output = elgg_view_field([
	'#type' => 'fieldset',
	'#label' => elgg_echo('event_manager:edit:form:organizer'),
	'align' => 'horizontal',
	'fields' => [
		[
			'#type' => 'text',
			'#class' => 'elgg-field-stretch',
			'#help' => elgg_echo('event_manager:edit:form:organizer:help'),
			'name' => 'organizer',
			'value' => $organizer,
		],
		[
			'#html' => elgg_echo('event_manager:edit:form:users:or'),
		],
		[
			'#type' => 'button',
			'text' => elgg_echo('event_manager:edit:form:users:add'),
			'class' => ['elgg-button-action', 'elgg-toggle'],
			'data-toggle-selector' => '.event-manager-contact-organizer-guids',
		],
	],
]);

$field_classes = ['event-manager-contact-organizer-guids'];
if (empty($organizer_guids)) {
	$field_classes[] = 'hidden';
}

$output .= elgg_view_field([
	'#type' => 'userpicker',
	'#label' => elgg_echo('event_manager:edit:form:organizer_guids'),
	'#class' => $field_classes,
	'name' => 'organizer_guids',
	'values' => $organizer_guids,
	'show_friends' => false,
]);

$output .= elgg_view_field([
	'#type' => 'fieldset',
	'#label' => elgg_echo('event_manager:edit:form:contact_details'),
	'align' => 'horizontal',
	'fields' => [
		[
			'#type' => 'text',
			'#class' => 'elgg-field-stretch',
			'#help' => elgg_echo('event_manager:edit:form:contact_details:help'),
			'name' => 'contact_details',
			'value' => $contact_details,
		],
		[
			'#html' => elgg_echo('event_manager:edit:form:users:or'),
		],
		[
			'#type' => 'button',
			'text' => elgg_echo('event_manager:edit:form:users:add'),
			'class' => ['elgg-button-action', 'elgg-toggle'],
			'data-toggle-selector' => '.event-manager-contact-contact-guids',
		],
	],
]);

$field_classes = ['event-manager-contact-contact-guids'];
if (empty($contact_guids)) {
	$field_classes[] = 'hidden';
}

$output .= elgg_view_field([
	'#type' => 'userpicker',
	'#label' => elgg_echo('event_manager:edit:form:contact_guids'),
	'#class' => $field_classes,
	'name' => 'contact_guids',
	'values' => $contact_guids,
	'show_friends' => false,
]);

$output .= elgg_view_field([
	'#type' => 'url',
	'#label' => elgg_echo('event_manager:edit:form:website'),
	'#help' => elgg_echo('event_manager:edit:form:website:help'),
	'name' => 'website',
	'value' => $website,
]);

echo $output;
