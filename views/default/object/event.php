<?php

if (elgg_extract('full_view', $vars)) {
	echo elgg_view("event_manager/event/view", $vars);
	return;
}

$event = elgg_extract('entity', $vars);

if (elgg_in_context('maps')) {
	$output = '<div class="gmaps_infowindow">';
	$output .= '<div class="gmaps_infowindow_text">';
	$output .= '<div class="event_manager_event_view_owner"><a href="' . $event->getURL() . '">' . $event->title . '</a> (' . event_manager_format_date($event->getStartTimestamp()) . ')</div>';
	$output .= str_replace(',', '<br />', $event->location) . '<br /><br />' . $event->shortdescription . '<br /><br />';
	$output .= elgg_view("event_manager/event/actions", $vars) . '</div>';
	if ($event->icontime) {
		$output .= '<div class="gmaps_infowindow_icon"><img src="' . $event->getIconURL() . '" /></div>';
	}
	$output .= '</div>';

	echo $output;
	return;
}

$content = '';
$subtitle = '';

if (!elgg_in_context('widgets')) {
	$subtitle = elgg_view('page/elements/by_line', $vars);
	
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

$content .= elgg_view('event_manager/event/actions', $vars);

$icon = elgg_view_entity_icon($event, 'date');

$menu = elgg_view_menu('entity', [
	'entity' => $vars['entity'],
	'handler' => 'event',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
]);

$params = [
	'entity' => $event,
	'metadata' => $menu,
	'subtitle' => $subtitle,
	'tags' => false,
	'content' => $content,
];
$params = $params + $vars;

$list_body = elgg_view('object/elements/summary', $params);

echo elgg_view_image_block($icon, $list_body);
