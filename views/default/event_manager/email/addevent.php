<?php
/**
 * Add links to the footer of the e-mail notification to add the event to your calendar
 *
 * @uses $vars['entity'] The event to generate links for
 */

if (elgg_get_plugin_setting('add_event_to_calendar', 'event_manager') === 'no') {
	// setting set to not show links
	return;
}

$event = elgg_extract('entity', $vars);
if (!($event instanceof Event)) {
	return;
}

// "outlook" or "google" or "appleical" or "outlookcom" or "yahoo"
$services = (array) elgg_extract('services', $vars, ['appleical', 'google', 'outlook', 'outlookcom', 'yahoo']);
if (empty($services)) {
	return;
}

echo '<br /><br />';
echo '<span class="event-manager-email-addevent-title">' . elgg_echo('event_manager:addevent:mail:title') . '</span>';
echo '<br /><br />';

$description = '';
if (!empty($event->location)) {
	// add venue to description
	$description .= $event->venue . PHP_EOL;
}

// removing HTML and shorter because of URL length limitations
$description .= $event->getExcerpt(500);

$url_params = [
	'client' => 'ak1qmrp10zvwxx2cimhv206',
	'date_format' => 'DD/MM/YYYY',
	'start' => $event->getStartDate('d/m/Y'),
	'starttime' => $event->getStartDate('H:i:00'),
	'end' => $event->getEndDate('d/m/Y'),
	'endtime' => $event->getEndDate('H:i:00'),
	'title' => $event->title,
	'description' => $description,
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
