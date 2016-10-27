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
	$contact_information .= '<div class="clearfix">';
	$contact_information .= '<div class="elgg-col elgg-col-1of6">' . elgg_view_icon('user', ['title' => elgg_echo('event_manager:edit:form:organizer')]) . '</div>';
	$contact_information .= '<div class="elgg-col elgg-col-5of6">' . $organizer . '</div>';
	$contact_information .= '</div>';
}

if ($contact_details) {
	$contact_information .= '<div class="clearfix">';
	$contact_information .= '<div class="elgg-col elgg-col-1of6">' . elgg_view_icon('info-circle', ['title' => elgg_echo('event_manager:edit:form:contact_details')]) . '</div>';
	$contact_information .= '<div class="elgg-col elgg-col-5of6">' . elgg_view('output/text', ['value' => $contact_details]) . '</div>';
	$contact_information .= '</div>';
}

if ($website) {
	if (!preg_match('~^https?\://~i', $website)) {
		$website = "http://$website";
	}

	$contact_information .= '<div class="clearfix">';
	$contact_information .= '<div class="elgg-col elgg-col-1of6">' . elgg_view_icon('globe', ['title' => elgg_echo('event_manager:edit:form:website')]) . '</div>';
	$contact_information .= '<div class="elgg-col elgg-col-5of6">' . elgg_view('output/url', ['href' => $website]) . '</div>';
	$contact_information .= '</div>';
}

if (!empty($contact_information)) {
// 	$contact_information = elgg_format_element('table', [], $contact_information);
	echo elgg_view_module('aside', elgg_echo('event_manager:edit:form:tabs:contact'), $contact_information);
}

$contact_guids = $event->contact_guids;
if (!empty($contact_guids)) {
	if (!is_array($contact_guids)) {
		$contact_guids = [$contact_guids];
	}
	$contact_content = '';
	foreach ($contact_guids as $contact_guid) {
		$member_entity = get_entity($contact_guid);
		if (empty($member_entity)) {
			continue;
		}
		$member_icon = elgg_view_entity_icon($member_entity, 'tiny', ['event' => $event]);
		$member_name = elgg_view('output/url', [
			'href' => $member_entity->getURL(),
			'text' => $member_entity->name,
		]);
		$contact_content .= elgg_view_image_block($member_icon, $member_name, ['class' => 'pan']);
	}
	
	echo elgg_view_module('aside', elgg_echo('event_manager:event:view:contact_persons'), $contact_content);
}