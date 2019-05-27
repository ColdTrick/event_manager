<?php

$event = elgg_extract('entity', $vars);
if (!$event instanceof \Event) {
	return;
}

$event_start = \Elgg\Values::normalizeTime($event->getStartDate('d-m-Y H:i:s'));
$event_end = \Elgg\Values::normalizeTime($event->getEndDate('d-m-Y H:i:s'));

$when_title = $event_start->formatLocale('l, M j Y');
$when_subtitle = '';

if (!$event->isMultiDayEvent()) {
	// same day event
	$when_subtitle .= $event_start->formatLocale('H:i') . ' ' . strtolower(elgg_echo('event_manager:date:to')) . ' ' . $event_end->formatLocale('H:i');
} else {
	$when_title .= ' ' . $event_start->formatLocale('H:i');
	
	$when_subtitle .= strtolower(elgg_echo('event_manager:date:to')) . ' ';
	$when_subtitle .= $event_end->formatLocale('l, M j Y H:i');
}

$when = elgg_format_element('div', ['class' => 'event-manager-event-when-title'], $when_title);
if (!empty($when_subtitle)) {
	$when .= elgg_format_element('div', ['class' => 'event-manager-event-when-subtitle'], $when_subtitle);
}

echo elgg_view_image_block(elgg_view_icon('calendar', ['class' => 'elgg-icon-hover']), $when, ['class' => 'event-manager-event-when']);
