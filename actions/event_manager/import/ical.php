<?php

use Kigkonsult\Icalcreator\IcalInterface;
use Kigkonsult\Icalcreator\Vcalendar;
use Kigkonsult\Icalcreator\Vevent;

$calendar_type = get_input('calendar_type', 'all');

$owner = (array) get_input('owner');
$group = (array) get_input('group');
$file = elgg_get_uploaded_file('import');

if (!$file) {
	return elgg_error_response(elgg_echo('event_manager:ical_direct:import:errors:missingfile'));
}

try {
	$vcalendar = Vcalendar::factory(
		[
			IcalInterface::UNIQUE_ID => 'https://github.com/ColdTrick/event_manager',
		]
	);
} catch (Exception $e) {
	return elgg_error_response(elgg_echo('event_manager:ical_direct:import:errors:errorinstantiatingcalendar', [$e]));
}

try {
	$vcalendar->parse($file->getContent());
} catch (Exception $e) {
	return elgg_error_response(elgg_echo('event_manager:ical_direct:import:errors:errorpparsingcalendar', [$e]));
}

$event_counter = 0;

/** @var Vevent $component */
foreach ($vcalendar->getComponents('Vevent') as $component) {
	try {
		$event = Event::fromVEvent($component);
	} catch (Exception $e) {
		return elgg_error_response(elgg_echo('event_manager:ical_direct:import:errors:errorconvertingevent', [$e]));
	}

	switch ($calendar_type) {
		case 'group':
			if (empty($group)) {
				return elgg_error_response(elgg_echo('event_manager:ical_direct:import:errors:groupempty'));
			}

			$group_guid = $group[0];
			if (!get_entity($group_guid) instanceof \ElggGroup) {
				return elgg_error_response(elgg_echo('event_manager:ical_direct:import:errors:invalidgroup'));
			}

			if (!elgg_is_admin_logged_in() && !$group->canWriteToContainer(elgg_get_logged_in_user_guid())) {
				return elgg_error_response(elgg_echo('event_manager:ical_direct:import:errors:grouppermission'));
			}

			$event->setContainerGUID($group_guid);
			break;
		case 'owner':
			if (empty($owner)) {
				return elgg_error_response(elgg_echo('event_manager:ical_direct:import:errors:ownerempty'));
			}

			$owner_guid = $owner[0];
			if (!get_entity($owner_guid) instanceof \ElggUser) {
				return elgg_error_response(elgg_echo('event_manager:ical_direct:import:errors:invalidgroup'));
			}

			if (!elgg_is_admin_logged_in() && (int) $owner_guid !== elgg_get_logged_in_user_guid()) {
				return elgg_error_response(elgg_echo('event_manager:ical_direct:import:errors:ownermismatch'));
			}

			$event->owner_guid = $owner_guid;
			break;
	}

	$event->save();
	$event_counter++;
}

return elgg_ok_response(
	'',
	elgg_echo('event_manager:ical_direct:import:success', [$event_counter])
);
