<?php

use Kigkonsult\Icalcreator\IcalInterface;
use Kigkonsult\Icalcreator\Vcalendar;
use Kigkonsult\Icalcreator\Vevent;

$calendar_type = get_input('calendar_type', 'all');

$owner = (array) get_input('owner');
$owner_guid = 0;
$group = (array) get_input('group');
$group_guid = 0;
$file = elgg_get_uploaded_file('import');

switch ($calendar_type) {
	case 'group':
		if (empty($group)) {
			return elgg_error_response(elgg_echo('event_manager:ical_direct:import:errors:groupempty'));
		}

		$group_guid = $group[0];
		$group_entity = get_entity($group_guid);
		if (!get_entity($group_guid) instanceof \ElggGroup) {
			return elgg_error_response(elgg_echo('event_manager:ical_direct:import:errors:invalidgroup'));
		}

		if (!elgg_is_admin_logged_in() && !$group_entity->canWriteToContainer(elgg_get_logged_in_user_guid())) {
			return elgg_error_response(elgg_echo('event_manager:ical_direct:import:errors:grouppermission'));
		}
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
		break;
}

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
	$event = Event::fromVEvent($component);

	switch ($calendar_type) {
		case 'group':
			$event->setContainerGUID($group_guid);
			break;
		case 'owner':
			$event->owner_guid = $owner_guid;
			break;
	}

	$event->save();
	$event_counter++;
}

$message = elgg_echo('event_manager:ical_direct:import:success', [$event_counter]);

return elgg_ok_response(
	'',
	$message
);
