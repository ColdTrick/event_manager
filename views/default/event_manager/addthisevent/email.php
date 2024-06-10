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
if (!$event instanceof \Event) {
	return;
}

$services = (array) elgg_extract('services', $vars, ['google', 'yahoo', 'office365', 'outlookcom', 'outlook', 'appleical']);
$service_links = [];

foreach ($services as $service) {
	if (elgg_get_plugin_setting("show_service_{$service}", 'event_manager')) {
		$service_links[] = $service;
	}
}

if (empty($service_links)) {
	return;
}

echo '<br /><br />';
echo elgg_format_element('span', ['class' => 'event-manager-email-addevent-title'], elgg_echo('event_manager:addevent:mail:title'));
echo '<br /><br />';

$description = '';
if (!empty($event->location)) {
	// add venue to description
	$description .= $event->venue . PHP_EOL;
}

// removing HTML and shorter because of URL length limitations
$description .= $event->getExcerpt(500) . PHP_EOL . PHP_EOL;
$description .= $event->getURL();

$url_params = [
	'client' => elgg_get_plugin_setting('add_event_license', 'event_manager'),
	'date_format' => 'DD/MM/YYYY',
	'start' => $event->getStartDate('d/m/Y'),
	'starttime' => $event->getStartDate('H:i:00'),
	'end' => $event->getEndDate('d/m/Y'),
	'endtime' => $event->getEndDate('H:i:00'),
	'title' => $event->getDisplayName(),
	'location' => $event->location ?: $event->venue,
	'description' => $description,
];

echo '<table><tr>';
foreach ($service_links as $service) {
	$url_params['service'] = $service;

	echo elgg_format_element('td', [
		'class' => 'event-manager-email-addevent event-manager-email-addevent-' . $service,
	], elgg_view_url(elgg_http_add_url_query_elements('https://addevent.com/dir/', $url_params), elgg_echo('event_manager:addevent:mail:service:' . $service)));
}

echo '</tr></table>';
