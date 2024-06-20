<?php

use ColdTrick\EventManager\Bootstrap;
use ColdTrick\EventManager\Event\Day;
use ColdTrick\EventManager\Event\Slot;
use Elgg\Router\Middleware\Gatekeeper;

require_once(dirname(__FILE__) . '/lib/functions.php');

$composer_path = '';
if (is_dir(__DIR__ . '/vendor')) {
	$composer_path = __DIR__ . '/';
}

return [
	'plugin' => [
		'version' => '18.0.2',
	],
	'bootstrap' => Bootstrap::class,
	'settings' => [
		'maps_provider' => 'osm',
		'osm_default_location' => 'Netherlands',
		'osm_default_location_lat' => 52,
		'osm_default_location_lng' => 6,
		'osm_default_zoom' => 7,
		'osm_detail_zoom' => 12,
		'who_create_site_events' => 'everyone',
		'who_create_group_events' => 'members',
		'rsvp_interested' => 'yes',
		'event_mail' => 0,
		'add_event_to_calendar' => 'yes',
		'show_service_google' => 1,
		'show_service_yahoo' => 1,
		'show_service_office365' => 1,
		'show_service_outlookcom' => 1,
		'show_service_outlook' => 1,
		'show_service_appleical' => 1,
	],
	'entities' => [
		[
			'type' => 'object',
			'subtype' => 'event',
			'class' => \Event::class,
			'capabilities' => [
				'commentable' => true,
				'searchable' => true,
				'likable' => true,
				'restorable' => true,
			],
		],
		[
			'type' => 'object',
			'subtype' => 'eventregistrationquestion',
			'class' => \EventRegistrationQuestion::class,
			'capabilities' => [
				'commentable' => false,
			],
		],
		[
			'type' => 'object',
			'subtype' => 'eventregistration',
			'class' => \EventRegistration::class,
			'capabilities' => [
				'commentable' => false,
			],
		],
		[
			'type' => 'object',
			'subtype' => 'eventday',
			'class' => Day::class,
			'capabilities' => [
				'commentable' => false,
			],
		],
		[
			'type' => 'object',
			'subtype' => 'eventslot',
			'class' => Slot::class,
			'capabilities' => [
				'commentable' => false,
			],
		],
		[
			'type' => 'object',
			'subtype' => 'eventmail',
			'class' => \EventMail::class,
			'capabilities' => [
				'commentable' => false,
			],
		],
	],
	'views' => [
		'default' => [
			'fullcalendar.js' => $composer_path . 'vendor/npm-asset/fullcalendar/index.global.min.js',
			'fullcalendar/translations.js' => $composer_path . 'vendor/npm-asset/fullcalendar--core/locales-all.global.min.js',
		],
	],
	'view_extensions' => [
		'elgg.css' => [
			'event_manager/site.css' => [],
			'event_manager/addthisevent/button.css' => [],
		],
		'email/email.css' => [
			'event_manager/addthisevent/email.css' => [],
		],
		'event_manager/addthisevent/button.mjs' => [
			'event_manager/addthisevent/settings.js' => [],
		],
		'fullcalendar.js' => [
			'fullcalendar/translations.js' => [],
		],
	],
	'view_options' => [
		'event_manager/event/attendees_list' => ['ajax' => true],
		'event_manager/listing/calendar.mjs' => ['simplecache' => true],
		'event_manager/event/popup' => ['ajax' => true],
		'event_manager/forms/program/day' => ['ajax' => true],
		'event_manager/forms/program/slot' => ['ajax' => true],
		'forms/event_manager/event/copy' => ['ajax' => true],
	],
	'routes' => [
		'add:object:event' => [
			'path' => '/event/add/{guid}',
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
		'mail:object:event' => [
			'path' => '/event/mail/{guid}',
			'resource' => 'event/mail',
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
		'collection:object:event:owner' => [
			'path' => '/event/owner/{username}',
			'resource' => 'event/owner',
			'middleware' => [
				\Elgg\Router\Middleware\UserPageOwnerGatekeeper::class,
			],
		],
		'collection:object:event:attending' => [
			'path' => '/event/attending/{username}',
			'resource' => 'event/attending',
			'middleware' => [
				\Elgg\Router\Middleware\UserPageOwnerGatekeeper::class,
			],
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
		'default:event_manager:calendar' => [
			'path' => '/event_manager/calendar',
			'controller' => \ColdTrick\EventManager\Controllers\Calendar::class,
			'requirements' => [
				'start' => '.+',
				'end' => '.+',
			],
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
		'event_manager/event/mail' => [],
		
		'event_manager/attendees/export' => [],
		'event_manager/attendees/move_to_attendees' => [],
		
		'event_manager/maps/data' => ['access' => 'public'],
		
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
	'events' => [
		'container_logic_check' => [
			'object' => [
				\ColdTrick\EventManager\GroupToolContainerLogicCheck::class => [],
				'\ColdTrick\EventManager\Access::containerLogicCheck' => [],
			],
		],
		'cron' => [
			'daily' => [
				'\ColdTrick\EventManager\Notifications\CreateEventEventHandler::enqueueDelayedNotifications' => [],
			],
		],
		'elgg.data' => [
			'page' => [
				'\ColdTrick\EventManager\Js::getJsConfig' => [],
			],
		],
		'enqueue' => [
			'notification' => [
				'\ColdTrick\EventManager\Notifications\CreateEventEventHandler::preventEnqueue' => [],
			],
			'notifications' => [
				'\ColdTrick\EventManager\Notifications\CreateEventEventHandler::trackNotificationSent' => [],
			],
		],
		'entity:icon:url' => [
			'object' => [
				'\ColdTrick\EventManager\Icons::getEventRegistrationIconURL' => [],
			],
		],
		'entity:url' => [
			'object' => [
				'\ColdTrick\EventManager\Widgets::getEventsUrl' => [],
			],
		],
		'export_attendee' => [
			'event' => [
				'\ColdTrick\EventManager\Attendees::exportBaseAttributes' => ['priority' => 100],
				'\ColdTrick\EventManager\Attendees::exportQuestionData' => ['priority' => 200],
				'\ColdTrick\EventManager\Attendees::exportProgramData' => ['priority' => 300],
			],
		],
		'export:metadata_names' => [
			'elasticsearch' => [
				'\ColdTrick\EventManager\Search::exportMetadataNames' => [],
			],
			'opensearch' => [
				'\ColdTrick\EventManager\Search::exportMetadataNames' => [],
			],
		],
		'handlers' => [
			'widgets' => [
				'\ColdTrick\EventManager\Widgets::registerHandlers' => [],
			],
		],
		'prepare' => [
			'system:email' => [
				'\ColdTrick\EventManager\Notifications::prepareEventRegistrationSender' => [],
			],
		],
		'register' => [
			'menu:entity' => [
				'\ColdTrick\EventManager\Menus\Entity::registerAttendeeActions' => [],
				'\ColdTrick\EventManager\Menus\Entity::registerEventUnsubscribe' => ['priority' => 600],
				'\ColdTrick\EventManager\Menus\Entity::registerMailAttendees' => [],
			],
			'menu:event_attendees' => [
				'\ColdTrick\EventManager\Menus::registerEventAttendees' => [],
			],
			'menu:event_files' => [
				'\ColdTrick\EventManager\Menus::registerEventFiles' => [],
			],
			'menu:event:rsvp' => [
				'\ColdTrick\EventManager\Menus::registerRsvp' => [],
			],
			'menu:filter:events' => [
				'\ColdTrick\EventManager\Menus::registerEventsList' => [],
				'\ColdTrick\EventManager\Menus\Filter::registerViewTypes' => [],
			],
			'menu:owner_block' => [
				'\ColdTrick\EventManager\Menus::registerGroupOwnerBlock' => [],
				'\ColdTrick\EventManager\Menus::registerUserOwnerBlock' => [],
			],
			'menu:site' => [
				'\ColdTrick\EventManager\Menus\Site::registerEvents' => [],
			],
			'menu:title:object:event' => [
				\Elgg\Notifications\RegisterSubscriptionMenuItemsHandler::class => [],
			],
		],
		'search:fields' => [
			'object:event' => [
				'\ColdTrick\EventManager\Search::addFields' => [],
			],
		],
		'seeds' => [
			'database' => [
				'ColdTrick\EventManager\Seeder::register' => [],
			],
		],
		'send:after' => [
			'notifications' => [
				'\ColdTrick\EventManager\Notifications::sendAfterEventMail' => ['priority' => 99999],
			],
		],
		'update:after' => [
			'object' => [
				'\ColdTrick\EventManager\Access::updateEvent' => [],
			],
		],
		'view_vars' => [
			'event_manager/listing/map' => [
				'\ColdTrick\EventManager\Views::loadLeafletCss' => [],
			],
			'input/objectpicker/item' => [
				'\ColdTrick\EventManager\ObjectPicker::customText' => [],
			],
			'widgets/content_by_tag/display/simple' => [
				'\ColdTrick\EventManager\Widgets::contentByTagEntityTimestamp' => [],
			],
			'widgets/content_by_tag/display/slim' => [
				'\ColdTrick\EventManager\Widgets::contentByTagEntityTimestamp' => [],
			],
		],
	],
	'notifications' => [
		'object' => [
			'event' => [
				'create' => \ColdTrick\EventManager\Notifications\CreateEventEventHandler::class,
			],
			'eventmail' => [
				'create' => \ColdTrick\EventManager\Notifications\CreateEventMailEventHandler::class,
			],
		],
	],
];
