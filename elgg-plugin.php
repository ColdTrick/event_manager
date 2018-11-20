<?php

use ColdTrick\EventManager\Bootstrap;

define('DOMPDF_ENABLE_AUTOLOAD', false);

define('EVENT_MANAGER_RELATION_ATTENDING', 'event_attending');
define('EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST', 'event_waitinglist');
define('EVENT_MANAGER_RELATION_ATTENDING_PENDING', 'event_pending');
define('EVENT_MANAGER_RELATION_EXHIBITING', 'event_exhibiting');
define('EVENT_MANAGER_RELATION_ORGANIZING', 'event_organizing');
define('EVENT_MANAGER_RELATION_PRESENTING', 'event_presenting');
define('EVENT_MANAGER_RELATION_INTERESTED', 'event_interested');
define('EVENT_MANAGER_RELATION_UNDO', 'event_undo');

define('EVENT_MANAGER_RELATION_SLOT_REGISTRATION', 'event_slot_registration');
define('EVENT_MANAGER_RELATION_SLOT_REGISTRATION_WAITINGLIST', 'event_slot_registration_waitinglist');
define('EVENT_MANAGER_RELATION_SLOT_REGISTRATION_PENDING', 'event_slot_registration_pending');

require_once(dirname(__FILE__) . '/lib/functions.php');

$composer_path = '';
if (is_dir(__DIR__ . '/vendor')) {
	$composer_path = __DIR__ . '/';
}

return [
	'bootstrap' => Bootstrap::class,
	'settings' => [
		'maps_provider' => 'google',
		'google_maps_default_location' => 'Netherlands',
		'google_maps_default_zoom' => 10,
		'google_maps_detail_zoom' => 12,
		'osm_default_location' => 'Netherlands',
		'osm_default_location_lat' => 52,
		'osm_default_location_lng' => 6,
		'osm_default_zoom' => 7,
		'osm_detail_zoom' => 12,
		'who_create_site_events' => 'everyone',
		'who_create_group_events' => 'members',
		'rsvp_interested' => 'yes',
		'add_event_to_calendar' => 'yes',
	],
	'entities' => [
		[
			'type' => 'object',
			'subtype' => 'event',
			'class' => 'Event',
			'searchable' => true,
		],
		[
			'type' => 'object',
			'subtype' => 'eventregistrationquestion',
			'class' => 'EventRegistrationQuestion',
		],
		[
			'type' => 'object',
			'subtype' => 'eventregistration',
			'class' => 'EventRegistration',
		],
		[
			'type' => 'object',
			'subtype' => 'eventday',
			'class' => '\ColdTrick\EventManager\Event\Day',
		],
		[
			'type' => 'object',
			'subtype' => 'eventslot',
			'class' => '\ColdTrick\EventManager\Event\Slot',
		],
	],
	'views' => [
		'default' => [
	       'js/fullcalendar.js' => $composer_path . 'vendor/bower-asset/fullcalendar/dist/fullcalendar.min.js',
	       'js/moment.js' => $composer_path . 'vendor/bower-asset/moment/min/moment-with-locales.min.js',
	       'css/event_manager/fullcalendar.css' => $composer_path . 'vendor/bower-asset/fullcalendar/dist/fullcalendar.min.css',
	    ],
	],
	'routes' => [

	],
	'actions' => [
		'event_manager/event/edit' => [],
		'event_manager/event/copy' => [],
		'event_manager/event/rsvp' => [],
		'event_manager/event/upload' => [],
		'event_manager/event/deletefile' => [],
		'event_manager/event/search' => [],
		'event_manager/event/unsubscribe' => ['access' => 'public'],
		'event_manager/event/unsubscribe_confirm' => ['access' => 'public'],
		'event_manager/event/resend_confirmation' => [],
		'event_manager/event/register' => ['access' => 'public'],
		'event_manager/event/search' => ['access' => 'public'],
		
		'event_manager/attendees/export' => [],
		'event_manager/attendees/move_to_attendees' => [],
		
		'event_manager/registration/edit' => [],
		'event_manager/registration/pdf' => ['access' => 'public'],
		'event_manager/registration/confirm' => ['access' => 'public'],
		
		'event_manager/slot/save' => [],
		'event_manager/day/edit' => [],
	],
	'widgets' => [
		'events' => [
			'context' => ['index', 'dashboard', 'profile', 'groups'],
		],
		'highlighted_events' => [
			'context' => ['index', 'groups'],
			'multiple' => true,
		],
	],
];
