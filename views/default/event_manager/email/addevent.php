<?php
$event = elgg_extract('entity', $vars);

// "outlook" or "google" or "appleical" or "outlookcom" or "yahoo"
$services = (array) elgg_extract('services', $vars, ['appleical', 'google', 'outlook', 'outlookcom', 'yahoo']);
if (empty($services)) {
	return;
}

echo '<br /><br />';
echo '<span class="event-manager-email-addevent-title">' . elgg_echo('event_manager:addevent:mail:title') . '</span>';
echo '<br /><br />';

$url_params = [
	'client' => 'ak1qmrp10zvwxx2cimhv206',
	'date_format' => 'DD/MM/YYYY',
	'start' => $event->getStartDate('d/m/Y'),
	'starttime' => $event->getStartDate('H:i:00'),
	'end' => $event->getEndDate('d/m/Y'),
	'endtime' => $event->getEndDate('H:i:00'),
	'title' => $event->title,
	'description' => elgg_get_excerpt($event->description, 500),
	'location' => $event->location ?: $event->venue,
];

echo '<table><tr>';
foreach ($services as $service) {
	$url_params['service'] = $service;

	$link = elgg_view('output/url', [
		'href' => elgg_http_add_url_query_elements('https://addevent.com/dir/', $url_params),
		'text' => elgg_echo('event_manager:addevent:mail:service:' . $service),
	]);
	echo elgg_format_element('td', [
		'class' => 'event-manager-email-addevent event-manager-email-addevent-' . $service,
	], $link);
}
echo '</tr></table>';
