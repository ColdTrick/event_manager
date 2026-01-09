<?php

use Kigkonsult\Icalcreator\Vcalendar;
use Kigkonsult\Icalcreator\Vevent;

$calendar_type = get_input('calendar_type', 'all');

$owner = get_input('owner', '')[0];
$group = get_input('group', '')[0];
$file = elgg_get_uploaded_file('import');

$vcalendar = Vcalendar::factory(
	[
		Vcalendar::UNIQUE_ID => 'https://github.com/ColdTrick/event_manager',
	]
);
try {
	$vcalendar->parse($file->getContent());
} catch (Exception $e) {
	return elgg_error_response('Error parsing calendar: ' . $e);
}

$event_counter = 0;

/** @var Vevent $component */
foreach ($vcalendar->getComponents('Vevent') as $component) {
	try {
		$event = Event::fromVEvent($component);

		switch ($calendar_type) {
			case 'group':
				$event->setContainerGUID($group);
				break;
			case 'owner':
				$event->owner_guid = $owner;
				break;
		}

		$event->save();
		$event_counter++;
	} catch (Exception $e) {
		return elgg_error_response(
			elgg_echo('event_manager:ical_direct:import:failure', [$e])
		);
	}
}

return elgg_ok_response(
	'',
	elgg_echo('event_manager:ical_direct:import:success', [$event_counter])
);
