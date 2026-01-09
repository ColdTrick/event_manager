<?php
/** @global array $vars */
$guid = (int) elgg_extract('guid', $vars);
/** @noinspection PhpUnhandledExceptionInspection */
elgg_entity_gatekeeper($guid, 'object', \Event::SUBTYPE);

/* @var Event $event */
$event = get_entity($guid);

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
	->setXprop(Vcalendar::X_WR_CALDESC, 'Single exported event from event_manager')
	->setXprop(Vcalendar::X_WR_RELCALID, elgg_get_site_url() . '#' . $guid)
	->setXprop(Vcalendar::X_WR_TIMEZONE, date_default_timezone_get());


/** @noinspection PhpUnhandledExceptionInspection */
$vcalendar->setComponent($event->toVEvent());

/** @noinspection PhpUnhandledExceptionInspection */
echo $vcalendar->returnCalendar(true, false, false, 'export.ics');
