<?php

$event = elgg_extract('entity', $vars);
if (!($event instanceof Event)) {
	return;
}

$contact_information = '';

$website = $event->website;
$contact_details = $event->contact_details;
$twitter_hash = $event->twitter_hash;
$organizer = $event->organizer;

if ($organizer) {
	
	$contact_information .= '<label>' . elgg_echo('event_manager:edit:form:organizer') . '</label>';
	$contact_information .= '<div>' . $organizer . '</div>';
}

if ($contact_details) {

	$contact_information .= '<label>' . elgg_echo('event_manager:edit:form:contact_details') . '</label>';
	$contact_information .= '<div>' . elgg_view('output/text', ['value' => $contact_details]) . '</div>';
}

if ($website) {
	if (!preg_match('~^https?\://~i', $website)) {
		$website = "http://$website";
	}

	$contact_information .= '<label>' . elgg_echo('event_manager:edit:form:website') . '</label>';
	$contact_information .= '<div>' . elgg_view('output/url', ['href' => $website]) . '</div>';
}

if ($twitter_hash) {
	$contact_information .= '<label>' . elgg_echo('event_manager:edit:form:twitter_hash') . '</label>';
	$contact_information .= '<div>' . elgg_view('output/url', [
		'value' => 'http://twitter.com/search?q=' . urlencode($twitter_hash),
		'text' => elgg_view('output/text', ['value' => $twitter_hash]),
	]) . '</div>';
}

if (!empty($contact_information)) {
	echo elgg_view_module('aside', 'Contact details', $contact_information);
}