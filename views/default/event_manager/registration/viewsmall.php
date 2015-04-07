<?php

	// @todo where is this view used?

	$registration = $vars["entity"];
	$owner = $registration->getOwnerEntity();
	
	$output = "";
	$icon = "";
	
	$output .= '<div class="event_manager_registration_info">';
	$output .= '<a class="user" href="' . $owner->getURL() . '">' . $owner->name . '</a> - ' . friendly_time($registration->time_created) . '<br />';
	$output .= elgg_view("output/url", array("href" => $registration->getURL(), "text" => elgg_echo('event_manager:event:viewregistration')));
	$output .= '</div>';
	
	$output .= '<div class="event_manager_registration_links">';
	
	if ($registration->approved) {
		$output .= elgg_view("output/url", array("href" => "action/event_manager/registration/approve?guid=" . $registration->getGUID() . "&approve=0", "text" => elgg_echo('disapprove'), "is_action" => true));
	} else {
		$output .= elgg_view("output/url", array("href" => "action/event_manager/registration/approve?guid=" . $registration->getGUID() . "&approve=1", "text" => elgg_echo('approve'), "is_action" => true));
	}
	
	$output .= '</div>';

	$icon .= '<div class="event_manager_registration_icon">';
	$icon .= '<img src="' . elgg_get_site_url() . 'mod/event_manager/_graphics/icons/register_icon.png">';
	$icon .= '</div>';
	
	echo elgg_view_listing($icon, $output);