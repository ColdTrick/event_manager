<?php

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
require_once(dirname(__FILE__) . '/lib/hooks.php');
require_once(dirname(__FILE__) . '/lib/events.php');
require_once(dirname(__FILE__) . '/lib/page_handlers.php');

/**
 * Init function for this plugin
 *
 * @return void
 */
function event_manager_init() {
	$base_dir = dirname(__FILE__);
	
	elgg_register_library('dompdf', $base_dir . '/vendors/dompdf/dompdf_config.inc.php');
	
	// Register entity_type for search
	elgg_register_entity_type('object', Event::SUBTYPE);

	elgg_extend_view('css/elgg', 'event_manager/css/site');
	elgg_extend_view('js/elgg', 'js/event_manager/site.js');

	// notifications
	elgg_register_notification_event('object', Event::SUBTYPE, array('create'));
	elgg_register_plugin_hook_handler('prepare', 'notification:create:object:' . Event::SUBTYPE, 'event_manager_prepare_notification');

	// register ajax views
	elgg_register_ajax_view('event_manager/event/maps/route');
	elgg_register_ajax_view('event_manager/event/maps/select_location');
	elgg_register_ajax_view('event_manager/forms/program/day');
	elgg_register_ajax_view('event_manager/forms/program/slot');
	
	// add site menu item
	elgg_register_menu_item('site', [
		'name' => 'event_manager',
		'text' => elgg_echo('event_manager:menu:title'),
		'href' => '/events'
	]);

	// add group tool option
	if (event_manager_groups_enabled()) {
		add_group_tool_option('event_manager', elgg_echo('groups:enableevents'), true);
	}

	// add to group profile
	elgg_extend_view('groups/tool_latest', 'event_manager/group_module');

	// add widgets
	elgg_register_widget_type('events', elgg_echo('event_manager:widgets:events:title'), elgg_echo('event_manager:widgets:events:description'), array('index', 'dashboard', 'profile', 'groups'));

	// register js libraries
	elgg_register_simplecache_view('js/event_manager/googlemaps.js');
	
	elgg_register_js('addthisevent', 'mod/event_manager/vendors/addthisevent/atemay.js');

	// page handlers
	elgg_register_page_handler('events', 'event_manager_page_handler');

	// events
	elgg_register_event_handler('update', 'object', 'event_manager_update_object_handler');

	// hooks
	elgg_register_plugin_hook_handler('register', 'menu:user_hover', 'event_manager_user_hover_menu');
	elgg_register_plugin_hook_handler('register', 'menu:entity', 'event_manager_entity_menu', 600);
	elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'event_manager_owner_block_menu');

	elgg_register_plugin_hook_handler('permissions_check', 'object', 'event_manager_permissions_check_handler');
	elgg_register_plugin_hook_handler('entity:url', 'object', 'event_manager_widget_events_url');

	elgg_register_plugin_hook_handler('setting', 'plugin', 'event_manager_invalidate_cache');
	
	// actions
	elgg_register_action('event_manager/event/edit', $base_dir . '/actions/event/edit.php');
	elgg_register_action('event_manager/event/delete', $base_dir . '/actions/event/delete.php');
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

	elgg_register_action('event_manager/question/save_order', $base_dir . '/actions/question/save_order.php');
	elgg_register_action('event_manager/question/delete', $base_dir . '/actions/question/delete.php');
	elgg_register_action('event_manager/question/edit', $base_dir . '/actions/question/edit.php');

	elgg_register_action('event_manager/registration/edit', $base_dir . '/actions/registration/edit.php');
	elgg_register_action('event_manager/registration/approve', $base_dir . '/actions/registration/approve.php');
	elgg_register_action('event_manager/registration/pdf', $base_dir . '/actions/registration/pdf.php', 'public');
	elgg_register_action('event_manager/registration/confirm', $base_dir . '/actions/registration/confirm.php', 'public');

	elgg_register_action('event_manager/slot/save', $base_dir . '/actions/slot/save.php');
	elgg_register_action('event_manager/slot/delete', $base_dir . '/actions/slot/delete.php');

	elgg_register_action('event_manager/day/edit', $base_dir . '/actions/day/edit.php');
	elgg_register_action('event_manager/day/delete', $base_dir . '/actions/day/delete.php');
}

/**
 * Page setup function
 *
 * @return void
 */
function event_manager_pagesetup() {
	// @todo check if this can be better
	elgg_load_js('lightbox');
	elgg_load_css('lightbox');

	$maps_key = elgg_get_plugin_setting('google_api_key', 'event_manager');
	elgg_register_js('event_manager.maps.base', '//maps.googleapis.com/maps/api/js?key=' . $maps_key . '&sensor=true');
	
	$page_owner = elgg_get_page_owner_entity();
	if ($page_owner instanceof ElggGroup) {
		if ($page_owner->event_manager_enable == 'no') {
			elgg_unregister_widget_type('events');
		}
	}
}

// register default elgg events
elgg_register_event_handler('init', 'system', 'event_manager_init');
elgg_register_event_handler('pagesetup', 'system', 'event_manager_pagesetup');
