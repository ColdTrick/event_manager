<?php

$section = elgg_extract('section', $vars);

// use a view var hook to toggle default collapsability or per section
$collapsed = elgg_extract('collapsed', $vars, true);

$title = elgg_extract('title', $vars);
$body = elgg_extract('body', $vars);
$body_vars = elgg_extract('body_vars', $vars, []);
$entity = elgg_extract('entity', $body_vars);

if ($collapsed) {
	// check (for supported sections) if they need to show expanded
	switch ($section) {
		case 'contact':
			$organizer = elgg_extract('organizer', $body_vars);
			$contact_details = elgg_extract('contact_details', $body_vars);
			$website = elgg_extract('website', $body_vars);
			$organizer_guids = elgg_extract('organizer_guids', $body_vars);
			$contact_guids = elgg_extract('contact_guids', $body_vars);
			
			if (!empty($organizer) || !empty($organizer_guids) || !empty($contact_guids) || !empty($contact_details) || !empty($website)) {
				$collapsed = false;
			}
			break;
		case 'location':
			$venue = elgg_extract('venue', $body_vars);
			$location = elgg_extract('location', $body_vars);
			$region = elgg_extract('region', $body_vars);
			
			$region_options = event_manager_event_region_options();
			
			if (!empty($venue) || !empty($location) || (!empty($region) && $region_options)) {
				$collapsed = false;
			}
			break;
		case 'profile':
			$shortdescription = elgg_extract('shortdescription', $body_vars);
			$description = elgg_extract('description', $body_vars);
			
			if (!empty($shortdescription) || !empty($description)) {
				$collapsed = false;
			}
			break;
		case 'registration':
			$fee = elgg_extract('fee', $body_vars);
			$max_attendees = elgg_extract('max_attendees', $body_vars);
			
			if (!empty($fee) || !empty($max_attendees)) {
				$collapsed = false;
			}
			break;
		default:
			// unsupported sections are not collapsed
			$collapsed = false;
			break;
	}
}

$header = '';
if ($collapsed) {
	$header .= elgg_view('input/button', [
		'class' => "event-manager-edit-{$section}-toggle elgg-button-action float-alt man pan phs",
		'value' => elgg_echo('event_manager:edit:form:tabs:toggle'),
		'rel' => 'toggle',
		'data-toggle-slide' => 0,
		'data-toggle-selector' => ".event-manager-edit-{$section}-toggle",
	]);
	
	$body = elgg_format_element('div', [
		'class' => "hidden event-manager-edit-{$section}-toggle",
		'data-toggle-slide' => 0,
	], $body);
	
	$body .= elgg_format_element('div', [
		'class' => "event-manager-edit-{$section}-toggle",
		'data-toggle-slide' => 0,
	], elgg_echo("event_manager:edit:form:tabs:{$section}:toggle"));
}

if ($title) {
	$header .= elgg_format_element('h3', [], $title);
}
if (empty($header)) {
	$header = null;
}

$module_vars = [
	'id' => elgg_extract('id', $vars),
	'class' => ['event_tab'],
	'header' => $header,
];

if ($section == 'questions') {
	if (!($entity instanceof \Event)) {
		$module_vars['class'][] = 'hidden';
	} elseif (!$entity->getRegistrationFormQuestions(true) && !$entity->registration_needed) {
		$module_vars['class'][] = 'hidden';
	}
}

echo elgg_view_module('info', null, $body, $module_vars);