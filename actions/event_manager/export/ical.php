<?php

$calendar_type = get_input('calendar_type', 'all');
$date_format = elgg_get_config('date_format', elgg_echo('input:date_format'));

$start_date = get_input('start_date', '');
if ($start_date == '') {
	$start_date = time();
} else {
	$start_date = DateTime::createFromFormat($date_format, $start_date)->getTimestamp();
}

$end_date = get_input('end_date', '');
if ($end_date == '') {
	$end_date = DateTime::createFromFormat('U', time())
		->add(DateInterval::createFromDateString('1 month'))
		->getTimestamp();
} else {
	$end_date = DateTime::createFromFormat($date_format, $end_date)->getTimestamp();
}

$region = get_input('region', '');
$event_type = get_input('event_type', '');

$owner = get_input('owner', '');
$group = get_input('group', '');

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

if ($region != '') {
	$options['metadata_name_value_pairs'][] = [
		'name' => 'region',
		'operand' => 'IN',
		'value' => $region,
	];
}

if ($event_type != '') {
	$options['metadata_name_value_pairs'][] = [
		'name' => 'event_type',
		'operand' => 'IN',
		'value' => $event_type,
	];
}

switch ($calendar_type) {
	case 'group':
		$options['container_guids'] = [$group];
		break;
	case 'owner':
		$options['owner_guids'] = [$owner];
		break;
}

$events = elgg_get_entities($options);

use Kigkonsult\Icalcreator\Vcalendar;

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpClassConstantAccessedViaChildClassInspection */
$vcalendar = Vcalendar::factory(
	[
		Vcalendar::UNIQUE_ID => 'https://github.com/ColdTrick/event_manager',
	]
)
	->setMethod(Vcalendar::PUBLISH)
	->setXprop(Vcalendar::X_WR_CALNAME, 'Exported event')
	->setXprop(Vcalendar::X_WR_CALDESC, 'Exported events from event_manager')
	->setXprop(Vcalendar::X_WR_RELCALID, elgg_get_site_url())
	->setXprop(Vcalendar::X_WR_TIMEZONE, date_default_timezone_get());

foreach ($events as $event) {
	/** @noinspection PhpUnhandledExceptionInspection */
	$vcalendar->setComponent($event->toVEvent());
}

/** @noinspection PhpUnhandledExceptionInspection */
return elgg_download_response($vcalendar->createCalendar(), 'export.ics');
