<?php

$event = elgg_extract('entity', $vars);
if (!$event instanceof \Event) {
	return;
}

$full_format = elgg_echo('event_manager:date:view:full');
$short_format = elgg_echo('event_manager:date:view:short');
$time_format = elgg_echo('event_manager:date:view:time');

// use custom format to convert gmdate to localized
$event_start = \Elgg\Values::normalizeTime($event->getStartDate('d-m-Y H:i:s'));
$event_end = \Elgg\Values::normalizeTime($event->getEndDate('d-m-Y H:i:s'));

$when_title = $event_start->formatLocale($short_format);
$when_subtitle = '';

if (!$event->isMultiDayEvent()) {
	// same day event
	$when_subtitle .= $event_start->formatLocale($time_format) . ' ' . strtolower(elgg_echo('event_manager:date:to')) . ' ' . $event_end->formatLocale($time_format);
} else {
	$when_title .= ' ' . $event_start->formatLocale($time_format);
	
	$when_subtitle .= strtolower(elgg_echo('event_manager:date:to')) . ' ';
	$when_subtitle .= $event_end->formatLocale($full_format);
}

$when = elgg_format_element('div', ['class' => 'event-manager-event-when-title'], $when_title);
if (!empty($when_subtitle)) {
	$when .= elgg_format_element('div', ['class' => 'event-manager-event-when-subtitle'], $when_subtitle);
}

echo elgg_view_image_block(elgg_view_icon('calendar', ['class' => 'elgg-icon-hover']), $when, ['class' => 'event-manager-event-when']);
