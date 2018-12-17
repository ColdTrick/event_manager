<?php

$event = elgg_extract('entity', $vars);
if (!$event instanceof \Event) {
	return;
}

// event details
$event_banner_url = '';
if ($event->hasIcon('event_banner')) {
	$event_banner_url = $event->getIconURL('event_banner');
} elseif ($event->hasIcon('master')) {
	$event_banner_url = $event->getIconURL('master');
}

if (empty($event_banner_url)) {
	return;
}

echo elgg_format_element('div', [
	'class' => ['mbl', 'event-manager-event-banner'],
	'style' => "background-image: url('{$event_banner_url}');"
]);
