<?php

$event = elgg_extract('entity', $vars);
if (!$event instanceof \Event) {
	return;
}

if (!$event->hasIcon('header', 'header')) {
	return;
}

$event_banner_url = $event->getIconURL(['type' => 'header', 'size' => 'header']);

echo elgg_format_element('div', [
	'class' => ['mbl', 'event-manager-event-banner'],
	'style' => "background-image: url('{$event_banner_url}');"
]);
