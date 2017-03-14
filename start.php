<?php

define('DOMPDF_ENABLE_AUTOLOAD', false);

@include_once(dirname(__FILE__) . '/vendor/autoload.php');

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

/**
 * Init function for this plugin
 *
 * @return void
 */
function event_manager_init() {
	$base_dir = dirname(__FILE__);
	
	if (file_exists($base_dir . '/vendor/dompdf/dompdf/dompdf_config.inc.php')) {
		// normal plugin install
		elgg_register_library('dompdf', $base_dir . '/vendor/dompdf/dompdf/dompdf_config.inc.php');
	} elseif (file_exists(dirname(dirname($base_dir)) . '/vendor/dompdf/dompdf/dompdf_config.inc.php')) {
		// plugin installed via composer
		elgg_register_library('dompdf', dirname(dirname($base_dir)) . '/vendor/dompdf/dompdf/dompdf_config.inc.php');
	}
	// Register entity_type for search
	elgg_register_entity_type('object', Event::SUBTYPE);

	elgg_extend_view('css/elgg', 'css/event_manager.css');
	elgg_extend_view('css/elgg', 'css/addthisevent.css');
	elgg_extend_view('css/html_email_handler/notification', 'css/event_manager/email_addevent.css');
	elgg_extend_view('js/elgg', 'js/event_manager/site.js');
	elgg_extend_view('js/addthisevent.js', 'js/event_manager/addthisevent.settings.js');
	
	elgg_register_css('fullcalendar', elgg_get_simplecache_url('css/event_manager/fullcalendar'));
	
	// notifications
	elgg_register_notification_event('object', Event::SUBTYPE, ['create']);
	elgg_register_plugin_hook_handler('prepare', 'notification:create:object:' . Event::SUBTYPE, '\ColdTrick\EventManager\Notifications::prepareCreateEventNotification');

	// register ajax views
	elgg_register_ajax_view('event_manager/event/maps/route');
	elgg_register_ajax_view('event_manager/forms/program/day');
	elgg_register_ajax_view('event_manager/forms/program/slot');
	elgg_register_ajax_view('event_manager/calendar');
	
	// add site menu item
	elgg_register_menu_item('site', [
		'name' => 'event_manager',
		'text' => elgg_echo('event_manager:menu:title'),
		'href' => 'events',
	]);

	// add group tool option
	if (event_manager_groups_enabled()) {
		add_group_tool_option('event_manager', elgg_echo('groups:enableevents'), true);
	}

	// add to group profile
	elgg_extend_view('groups/tool_latest', 'event_manager/group_module');

	// add widgets
	elgg_register_widget_type('events', elgg_echo('event_manager:widgets:events:title'), elgg_echo('event_manager:widgets:events:description'), ['index', 'dashboard', 'profile', 'groups']);
	if (elgg_view_exists('input/objectpicker')) {
		elgg_register_widget_type('highlighted_events', elgg_echo('event_manager:widgets:highlighted_events:title'), elgg_echo('event_manager:widgets:highlighted_events:description'), ['index', 'groups'], true);
	}
	elgg_register_plugin_hook_handler('handlers', 'widgets', '\ColdTrick\EventManager\Widgets::registerHandlers');

	// register js libraries
	elgg_define_js('gmaps', [
		'src' => elgg_get_simplecache_url('js/hpneo/gmaps/gmaps.js'),
	]);
	elgg_define_js('event_manager/maps', ['src' => elgg_get_simplecache_url('js/event_manager/maps.js')]);

	// page handlers
	elgg_register_page_handler('events', '\ColdTrick\EventManager\PageHandler::events');

	// events
	elgg_register_event_handler('upgrade', 'system', '\ColdTrick\EventManager\Upgrade::fixClasses');
	elgg_register_event_handler('upgrade', 'system', '\ColdTrick\EventManager\Upgrade::migrateFilesFromUserToEvent');
	elgg_register_event_handler('upgrade', 'system', '\ColdTrick\EventManager\Upgrade::convertTimestamps');

	elgg_register_event_handler('update:after', 'object', '\ColdTrick\EventManager\Access::updateEvent');
	
	// hooks
	elgg_register_plugin_hook_handler('register', 'menu:filter', '\ColdTrick\EventManager\Menus::registerFilter');
	elgg_register_plugin_hook_handler('register', 'menu:user_hover', '\ColdTrick\EventManager\Menus::registerUserHover');
	elgg_register_plugin_hook_handler('register', 'menu:entity', '\ColdTrick\EventManager\Menus::registerEntity', 600);
	elgg_register_plugin_hook_handler('register', 'menu:owner_block', '\ColdTrick\EventManager\Menus::registerGroupOwnerBlock');
	elgg_register_plugin_hook_handler('register', 'menu:owner_block', '\ColdTrick\EventManager\Menus::registerUserOwnerBlock');
	elgg_register_plugin_hook_handler('register', 'menu:event_edit', '\ColdTrick\EventManager\Menus::registerEventEdit');
	elgg_register_plugin_hook_handler('register', 'menu:event_files', '\ColdTrick\EventManager\Menus::registerEventFiles');
	elgg_register_plugin_hook_handler('register', 'menu:events_list', '\ColdTrick\EventManager\Menus::registerEventsList');
	elgg_register_plugin_hook_handler('register', 'menu:river', '\ColdTrick\EventManager\Menus::stripEventRelationshipRiverMenuItems', 99999);

	elgg_register_plugin_hook_handler('entity:url', 'object', '\ColdTrick\EventManager\Widgets::getEventsUrl');
	elgg_register_plugin_hook_handler('entity:icon:sizes', 'object', '\ColdTrick\EventManager\Icons::getIconSizes');
	elgg_register_plugin_hook_handler('entity:icon:file', 'object', '\ColdTrick\EventManager\Icons::getIconFile');

	elgg_register_plugin_hook_handler('setting', 'plugin', '\ColdTrick\EventManager\Settings::clearCache');
	
	elgg_register_plugin_hook_handler('likes:is_likable', 'object:' . \Event::SUBTYPE, '\Elgg\Values::getTrue');
	elgg_register_plugin_hook_handler('supported_types', 'entity_tools', '\ColdTrick\EventManager\MigrateEvents::supportedSubtypes');
	
	// custom priorities for the following hooks allow others to influence export data order
	elgg_register_plugin_hook_handler('export_attendee', 'event', '\ColdTrick\EventManager\Attendees::exportBaseAttributes', 100);
	elgg_register_plugin_hook_handler('export_attendee', 'event', '\ColdTrick\EventManager\Attendees::exportQuestionData', 200);
	elgg_register_plugin_hook_handler('export_attendee', 'event', '\ColdTrick\EventManager\Attendees::exportProgramData', 300);

	elgg_register_plugin_hook_handler('view_vars', 'widgets/content_by_tag/display/simple', '\ColdTrick\EventManager\Widgets::contentByTagEntityTimestamp');
	elgg_register_plugin_hook_handler('view_vars', 'widgets/content_by_tag/display/slim', '\ColdTrick\EventManager\Widgets::contentByTagEntityTimestamp');
	
	elgg_register_plugin_hook_handler('view_vars', 'input/objectpicker/item', '\ColdTrick\EventManager\ObjectPicker::customText');
	
	// actions
	elgg_register_action('event_manager/event/edit', $base_dir . '/actions/event/edit.php');
	elgg_register_action('event_manager/event/delete', $base_dir . '/actions/event/delete.php');
	elgg_register_action('event_manager/event/copy', $base_dir . '/actions/event/copy.php');
	elgg_register_action('event_manager/event/rsvp', $base_dir . '/actions/event/rsvp.php');
	elgg_register_action('event_manager/event/upload', $base_dir . '/actions/event/upload.php');
	elgg_register_action('event_manager/event/deletefile', $base_dir . '/actions/event/deletefile.php');
	elgg_register_action('event_manager/event/search', $base_dir . '/actions/event/search.php');
	elgg_register_action('event_manager/event/unsubscribe', $base_dir . '/actions/event/unsubscribe.php', 'public');
	elgg_register_action('event_manager/event/unsubscribe_confirm', $base_dir . '/actions/event/unsubscribe_confirm.php', 'public');
	elgg_register_action('event_manager/event/resend_confirmation', $base_dir . '/actions/event/resend_confirmation.php');
	elgg_register_action('event_manager/event/register', $base_dir . '/actions/event/register.php', 'public');
	elgg_register_action('event_manager/event/search', $base_dir . '/actions/event/search.php', 'public');

	elgg_register_action('event_manager/attendees/export', $base_dir . '/actions/attendees/export.php');
	elgg_register_action('event_manager/attendees/move_to_attendees', $base_dir . '/actions/attendees/move_to_attendees.php');

	elgg_register_action('event_manager/registration/edit', $base_dir . '/actions/registration/edit.php');
	elgg_register_action('event_manager/registration/pdf', $base_dir . '/actions/registration/pdf.php', 'public');
	elgg_register_action('event_manager/registration/confirm', $base_dir . '/actions/registration/confirm.php', 'public');

	elgg_register_action('event_manager/slot/save', $base_dir . '/actions/slot/save.php');
	elgg_register_action('event_manager/slot/delete', $base_dir . '/actions/slot/delete.php');

	elgg_register_action('event_manager/day/edit', $base_dir . '/actions/day/edit.php');
	elgg_register_action('event_manager/day/delete', $base_dir . '/actions/day/delete.php');
	
	elgg_register_action('event_manager/upgrades/files_migration', $base_dir . '/actions/upgrades/files_migration.php', 'admin');
	elgg_register_action('event_manager/upgrades/convert_timestamps', $base_dir . '/actions/upgrades/convert_timestamps.php', 'admin');
}

// register default elgg events
elgg_register_event_handler('init', 'system', 'event_manager_init');
