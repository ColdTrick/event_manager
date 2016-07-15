<?php

$event = elgg_extract('entity', $vars);
if (!($event instanceof Event)) {
	return;
}

$contact_information = '';

$website = $event->website;
$contact_details = $event->contact_details;
$organizer = $event->organizer;

if ($organizer) {
	$contact_information .= '<tr>';
	$contact_information .= '<td class="prs">' . elgg_view_icon('user', ['title' => elgg_echo('event_manager:edit:form:organizer')]) . '</td>';
	$contact_information .= '<td>' . $organizer . '</td>';
	$contact_information .= '</tr>';
}

if ($contact_details) {
	$contact_information .= '<tr>';
	$contact_information .= '<td class="prs">' . elgg_view_icon('info-circle', ['title' => elgg_echo('event_manager:edit:form:contact_details')]) . '</td>';
	$contact_information .= '<td>' . elgg_view('output/text', ['value' => $contact_details]) . '</td>';
	$contact_information .= '</tr>';
}

if ($website) {
	if (!preg_match('~^https?\://~i', $website)) {
		$website = "http://$website";
	}

	$contact_information .= '<tr>';
	$contact_information .= '<td class="prs">' . elgg_view_icon('globe', ['title' => elgg_echo('event_manager:edit:form:website')]) . '</td>';
	$contact_information .= '<td>' . elgg_view('output/url', ['href' => $website]) . '</td>';
	$contact_information .= '</tr>';
}

if (!empty($contact_information)) {
	$contact_information = elgg_format_element('table', [], $contact_information);
	echo elgg_view_module('aside', 'Contact details', $contact_information);
}