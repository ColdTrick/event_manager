<?php

if (elgg_extract('full_view', $vars)) {
	echo elgg_view("event_manager/event/view", $vars);
	return;
}

$event = elgg_extract('entity', $vars);

if (elgg_in_context('maps')) {
	$output = '<div class="maps_infowindow clearfix">';
	$output .= '<div class="maps_infowindow_text">';
	$output .= '<div class="event_manager_event_view_owner"><a href="' . $event->getURL() . '">' . $event->getDisplayName() . '</a><br />' . event_manager_format_date($event->getStartTimestamp()) . '</div>';
	$output .= str_replace(',', '<br />', $event->location) . '<br /><br />' . $event->shortdescription . '<br /><br />';
	$output .= elgg_view('event_manager/event/rsvp', $vars) . '</div>';
	if ($event->icontime) {
		$output .= '<div class="maps_infowindow_icon"><img src="' . $event->getIconURL() . '" /></div>';
	}
	$output .= '</div>';

	echo $output;
	return;
}

$content = '';

if (!elgg_in_context('widgets')) {
	
	$location = $event->location;
	if ($location) {
		$content .= '<div>' . elgg_echo('event_manager:edit:form:location') . ': ';
		$content .= elgg_view('output/url', [
			'href' => $event->getURL() . '#location',
			'text' => $location,
		]);
		$content .= '</div>';
	}

	$excerpt = $event->getExcerpt();
	if ($excerpt) {
		$content .= '<div>' . $excerpt . '</div>';
	}
}

$content .= elgg_view('event_manager/event/rsvp', $vars);

$imprint = elgg_extract('imprint', $vars, []);

$attendee_count = $event->countAttendees();
if ($attendee_count > 0 || $event->openForRegistration()) {
	$imprint['attendee_count'] = [
		'icon_name' => 'users',
		'content' => elgg_echo('event_manager:event:relationship:event_attending:entity_menu', [$attendee_count]),
	];
}

$params = [
	'entity' => $event,
	'content' => $content,
	'imprint' => $imprint,
];
$params = $params + $vars;

$list_body = elgg_view('object/elements/summary', $params);

echo elgg_view_image_block(elgg_view_entity_icon($event, 'date'), $list_body);
