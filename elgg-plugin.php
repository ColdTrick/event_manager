<?php

use ColdTrick\EventManager\Bootstrap;
use Elgg\Router\Middleware\Gatekeeper;
use ColdTrick\EventManager\Event\Day;
use ColdTrick\EventManager\Event\Slot;

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
			'class' => Event::class,
			'searchable' => true,
		],
		[
			'type' => 'object',
			'subtype' => 'eventregistrationquestion',
			'class' => EventRegistrationQuestion::class,
		],
		[
			'type' => 'object',
			'subtype' => 'eventregistration',
			'class' => EventRegistration::class,
		],
		[
			'type' => 'object',
			'subtype' => 'eventday',
			'class' => Day::class,
		],
		[
			'type' => 'object',
			'subtype' => 'eventslot',
			'class' => Slot::class,
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
		'add:object:event' => [
			'path' => '/event/add/{guid?}',
			'resource' => 'event/add',
			'middleware' => [
				Gatekeeper::class,
			],
		],
		'edit:object:event' => [
			'path' => '/event/edit/{guid}',
			'resource' => 'event/edit',
			'middleware' => [
				Gatekeeper::class,
			],
		],
		'edit:object:event:program' => [
			'path' => '/event/edit_program/{guid}',
			'resource' => 'event/edit_program',
			'middleware' => [
				Gatekeeper::class,
			],
		],
		'edit:object:event:upload' => [
			'path' => '/event/upload/{guid}',
			'resource' => 'event/upload',
			'middleware' => [
				Gatekeeper::class,
			],
		],
		'view:object:event' => [
			'path' => '/event/view/{guid}/{title?}',
			'resource' => 'event/view',
		],
		'collection:object:event:waitinglist' => [
			'path' => '/event/waitinglist/{guid}',
			'resource' => 'event/waitinglist',
		],
		'default:object:event:unsubscribe:confirm' => [
			'path' => '/event/unsubscribe/confirm/{guid}/{code}',
			'resource' => 'event/unsubscribe/confirm',
		],
		'default:object:event:unsubscribe:request' => [
			'path' => '/event/unsubscribe/request/{guid}',
			'resource' => 'event/unsubscribe/request',
		],
		'default:object:event:register' => [
			'path' => '/event/register/{guid}/{relation?}',
			'resource' => 'event/register',
		],
		'default:object:eventregistration:confirm' => [
			'path' => '/eventregistration/confirm/{guid}',
			'resource' => 'eventregistration/confirm',
		],
		'view:object:eventregistration' => [
			'path' => '/eventregistration/view/{guid}',
			'resource' => 'eventregistration/view',
		],
		'default:object:eventregistration:completed' => [
			'path' => '/eventregistration/completed/{event_guid}/{object_guid}',
			'resource' => 'eventregistration/completed',
		],
		'collection:object:event:live' => [
			'path' => '/event/live/{guid?}',
			'resource' => 'event/live',
		],
		'collection:object:event:upcoming' => [
			'path' => '/event/upcoming/{guid?}',
			'resource' => 'event/upcoming',
		],
		'collection:object:event:map' => [
			'path' => '/event/map',
			'resource' => 'event/map',
		],
		'collection:object:event:calendar' => [
			'path' => '/event/calendar',
			'resource' => 'event/calendar',
		],
		'collection:object:event:owner' => [
			'path' => '/event/owner/{username?}',
			'resource' => 'event/owner',
		],
		'collection:object:event:attending' => [
			'path' => '/event/attending/{username?}',
			'resource' => 'event/attending',
		],
		'collection:object:event:group' => [
			'path' => '/event/group/{guid}',
			'resource' => 'event/upcoming',
		],
		'collection:object:event:attendees' => [
			'path' => '/event/attendees/{guid}/{relationship}',
			'resource' => 'event/attendees',
		],
		'collection:object:event:all' => [
			'path' => '/event/upcoming',
			'resource' => 'event/upcoming',
		],
		'default:object:event' => [
			'path' => '/event',
			'resource' => 'event/upcoming',
		],
	],
	'actions' => [
		'event_manager/event/edit' => [],
		'event_manager/event/copy' => [],
		'event_manager/event/rsvp' => ['access' => 'public'],
		'event_manager/event/upload' => [],
		'event_manager/event/deletefile' => [],
		'event_manager/event/unsubscribe' => ['access' => 'public'],
		'event_manager/event/unsubscribe_confirm' => ['access' => 'public'],
		'event_manager/event/resend_confirmation' => [],
		'event_manager/event/register' => ['access' => 'public'],
		
		'event_manager/attendees/export' => [],
		'event_manager/attendees/move_to_attendees' => [],
		
		'event_manager/maps/data' => ['access' => 'public'],
		
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
