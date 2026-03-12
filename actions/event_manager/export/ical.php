<?php

$calendar_type = get_input('calendar_type', 'all');
$date_format = elgg_get_config('date_format', elgg_echo('input:date_format'));

$start_date = (string) get_input('start_date');
if (empty($start_date)) {
	$start_date = time();
} else {
	$start_date = DateTime::createFromFormat($date_format, $start_date)->getTimestamp();
}

$end_date = (string) get_input('end_date');
if (empty($end_date)) {
	$end_date = DateTime::createFromFormat('U', time())
		->add(DateInterval::createFromDateString('1 month'))
		->getTimestamp();
} else {
	$end_date = DateTime::createFromFormat($date_format, $end_date)->getTimestamp();
}

$region = (string) get_input('region');
$event_type = (string) get_input('event_type');

$owner = (array) get_input('owner');
$group = (array) get_input('group');

$options = [
	'types' => ['object'],
	'subTypes' => [Event::SUBTYPE],
	'metadata_name_value_pairs' => [
		[
			'name' => 'event_start',
			'operand' => '>=',
			'value' => $start_date,
		],
		[
			'name' => 'event_end',
			'operand' => '<=',
			'value' => $end_date,
		],
	]
];

if (!empty($region)) {
	$options['metadata_name_value_pairs'][] = [
		'name' => 'region',
		'operand' => 'IN',
		'value' => $region,
	];
}

if (!empty($event_type)) {
	$options['metadata_name_value_pairs'][] = [
		'name' => 'event_type',
		'operand' => 'IN',
		'value' => $event_type,
	];
}

switch ($calendar_type) {
	case 'group':
		if (empty($group)) {
			return elgg_error_response(elgg_echo('event_manager:ical_direct:export:errors:groupempty'));
		}

		$options['container_guids'] = $group;
		break;
	case 'owner':
		if (empty($owner)) {
			return elgg_error_response(elgg_echo('event_manager:ical_direct:export:errors:ownerempty'));
		}

		if (!elgg_is_admin_logged_in() && (int) $owner[0] !== elgg_get_logged_in_user_guid()) {
			return elgg_error_response(elgg_echo('event_manager:ical_direct:export:errors:ownermismatch'));
		}

		$options['owner_guids'] = [$owner];
		break;
}

/** @var Event[] $events */
$events = elgg_get_entities($options);

use Kigkonsult\Icalcreator\IcalInterface;
use Kigkonsult\Icalcreator\Vcalendar;

try {
	$vcalendar = Vcalendar::factory(
		[
			IcalInterface::UNIQUE_ID => 'https://github.com/ColdTrick/event_manager',
		]
	)
		->setMethod(IcalInterface::PUBLISH)
		->setXprop(IcalInterface::X_WR_CALNAME, 'Exported event')
		->setXprop(IcalInterface::X_WR_CALDESC, 'Exported events from event_manager')
		->setXprop(IcalInterface::X_WR_RELCALID, elgg_get_site_url())
		->setXprop(IcalInterface::X_WR_TIMEZONE, date_default_timezone_get());
} catch (Exception $e) {
	return elgg_error_response(elgg_echo('event_manager:ical_direct:export:errors:errorinstantiatingcalendar', [$e]));
}

foreach ($events as $event) {
	try {
		$vcalendar->setComponent($event->toVEvent());
	} catch (Exception $e) {
		return elgg_error_response(elgg_echo('event_manager:ical_direct:export:errors:erroraddingevent', [$e]));
	}
}

try {
	return elgg_download_response($vcalendar->createCalendar(), 'export.ics');
} catch (Exception $e) {
	return elgg_error_response(elgg_echo('event_manager:ical_direct:export:errors:errorcreatingcalendar', [$e]));
}
