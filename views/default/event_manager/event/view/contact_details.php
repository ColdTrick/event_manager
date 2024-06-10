<?php

$event = elgg_extract('entity', $vars);
if (!$event instanceof \Event) {
	return;
}

$contact_information = '';

$website = $event->website;
$contact_details = $event->contact_details;
$organizer = $event->organizer;

if ($organizer) {
	$contact_information .= elgg_view_image_block(
		elgg_view_icon('user', ['title' => elgg_echo('event_manager:edit:form:organizer')]),
		elgg_view('output/longtext', ['value' => $organizer])
	);
}

if ($contact_details) {
	$contact_information .= elgg_view_image_block(
		elgg_view_icon('info-circle', ['title' => elgg_echo('event_manager:edit:form:contact_details')]),
		elgg_view('output/longtext', ['value' => $contact_details])
	);
}

if ($website) {
	if (!preg_match('~^https?\://~i', $website)) {
		$website = "http://{$website}";
	}
	
	$contact_information .= elgg_view_image_block(
		elgg_view_icon('globe', ['title' => elgg_echo('event_manager:edit:form:website')]),
		elgg_view_url($website)
	);
}

if (!empty($contact_information)) {
	echo elgg_view_module('event', elgg_echo('event_manager:edit:form:tabs:contact'), $contact_information, ['class' => 'event-manager-contact-details']);
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
		$contact_content .= elgg_view_image_block($member_icon, elgg_view_entity_url($member_entity), ['class' => 'pan']);
	}
	
	echo elgg_view_module('event', elgg_echo('event_manager:event:view:contact_persons'), $contact_content);
}
