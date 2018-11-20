<?php

namespace ColdTrick\EventManager;

use Elgg\DefaultPluginBootstrap;
use Event;

class Bootstrap extends DefaultPluginBootstrap {
	
	/**
	 * {@inheritdoc}
	 */
	public function init() {

		// add site menu item
		elgg_register_menu_item('site', [
			'name' => 'event_manager',
			'icon' => 'calendar-alt',
			'text' => elgg_echo('event_manager:menu:title'),
			'href' => 'events',
		]);
	
		// add group tool option
		if (event_manager_groups_enabled()) {
			elgg()->group_tools->register('event_manager', ['label' => elgg_echo('groups:enableevents')]);
		}
	
		// add widgets
		elgg_register_widget_type('events', elgg_echo('event_manager:widgets:events:title'), elgg_echo('event_manager:widgets:events:description'), ['index', 'dashboard', 'profile', 'groups']);
		elgg_register_widget_type('highlighted_events', elgg_echo('event_manager:widgets:highlighted_events:title'), elgg_echo('event_manager:widgets:highlighted_events:description'), ['index', 'groups'], true);
		
		// page handlers
		elgg_register_page_handler('events', '\ColdTrick\EventManager\PageHandler::events');
		
		$this->initLibraries();
		$this->initViews();
		$this->initEvents();
		$this->initRegisterHooks();
	}

	/**
	 * Register external libraries
	 *
	 * @return void
	 */
	protected function initLibraries() {
		$base_dir = dirname(__FILE__);
	
		if (file_exists($base_dir . '/vendor/dompdf/dompdf/dompdf_config.inc.php')) {
			// normal plugin install
			elgg_register_library('dompdf', $base_dir . '/vendor/dompdf/dompdf/dompdf_config.inc.php');
		} elseif (file_exists(dirname(dirname($base_dir)) . '/vendor/dompdf/dompdf/dompdf_config.inc.php')) {
			// plugin installed via composer
			elgg_register_library('dompdf', dirname(dirname($base_dir)) . '/vendor/dompdf/dompdf/dompdf_config.inc.php');
		}
		
		// register js libraries
		elgg_define_js('gmaps', [
			'src' => elgg_get_simplecache_url('js/hpneo/gmaps/gmaps.js'),
		]);
		elgg_define_js('event_manager/maps', ['src' => elgg_get_simplecache_url('js/event_manager/maps.js')]);
	
		// leafletjs
		elgg_define_js('leafletjs', [
			'src' => '//unpkg.com/leaflet@1.2.0/dist/leaflet.js',
		]);
		elgg_register_css('leafletjs', '//unpkg.com/leaflet@1.2.0/dist/leaflet.css');
		
		elgg_register_css('fullcalendar', elgg_get_simplecache_url('css/event_manager/fullcalendar'));
	}

	/**
	 * Init views
	 *
	 * @return void
	 */
	protected function initViews() {
		
		// extend views
		elgg_extend_view('css/elgg', 'css/event_manager.css');
		elgg_extend_view('css/elgg', 'css/addthisevent.css');
		elgg_extend_view('css/html_email_handler/notification', 'css/event_manager/email_addevent.css');
		elgg_extend_view('js/elgg', 'js/event_manager/site.js');
		elgg_extend_view('js/addthisevent.js', 'js/event_manager/addthisevent.settings.js');
		elgg_extend_view('groups/tool_latest', 'event_manager/group_module');
	
		// register ajax views
		elgg_register_ajax_view('event_manager/event/attendees_list');
		elgg_register_ajax_view('event_manager/event/maps/route');
		elgg_register_ajax_view('event_manager/forms/program/day');
		elgg_register_ajax_view('event_manager/forms/program/slot');
		elgg_register_ajax_view('event_manager/calendar');
		elgg_register_ajax_view('forms/event_manager/event/copy');
	}

	/**
	 * Init events
	 *
	 * @return void
	 */
	protected function initEvents() {
		
		elgg_register_notification_event('object', Event::SUBTYPE, ['create']);
		
		elgg_register_event_handler('update:after', 'object', '\ColdTrick\EventManager\Access::updateEvent');
	}
	
	/**
	 * Register plugin hooks
	 *
	 * @return void
	 */
	protected function initRegisterHooks() {
		$hooks = $this->elgg()->hooks;
		
		$hooks->registerHandler('elgg.data', 'site', '\ColdTrick\EventManager\Js::getJsConfig');
		$hooks->registerHandler('entity:url', 'object', '\ColdTrick\EventManager\Widgets::getEventsUrl');
		$hooks->registerHandler('entity:icon:sizes', 'object', '\ColdTrick\EventManager\Icons::getIconSizes');
		$hooks->registerHandler('entity:icon:file', 'object', '\ColdTrick\EventManager\Icons::getIconFile');
		$hooks->registerHandler('export_attendee', 'event', '\ColdTrick\EventManager\Attendees::exportBaseAttributes', 100);
		$hooks->registerHandler('export_attendee', 'event', '\ColdTrick\EventManager\Attendees::exportQuestionData', 200);
		$hooks->registerHandler('export_attendee', 'event', '\ColdTrick\EventManager\Attendees::exportProgramData', 300);
		$hooks->registerHandler('handlers', 'widgets', '\ColdTrick\EventManager\Widgets::registerHandlers');
		$hooks->registerHandler('likes:is_likable', 'object:' . \Event::SUBTYPE, '\Elgg\Values::getTrue');
		$hooks->registerHandler('prepare', 'notification:create:object:' . Event::SUBTYPE, '\ColdTrick\EventManager\Notifications::prepareCreateEventNotification');
		$hooks->registerHandler('register', 'menu:filter', '\ColdTrick\EventManager\Menus::registerFilter');
		$hooks->registerHandler('register', 'menu:user_hover', '\ColdTrick\EventManager\Menus::registerUserHover');
		$hooks->registerHandler('register', 'menu:entity', '\ColdTrick\EventManager\Menus::registerEntity', 600);
		$hooks->registerHandler('register', 'menu:owner_block', '\ColdTrick\EventManager\Menus::registerGroupOwnerBlock');
		$hooks->registerHandler('register', 'menu:owner_block', '\ColdTrick\EventManager\Menus::registerUserOwnerBlock');
		$hooks->registerHandler('register', 'menu:event_files', '\ColdTrick\EventManager\Menus::registerEventFiles');
		$hooks->registerHandler('register', 'menu:events_list', '\ColdTrick\EventManager\Menus::registerEventsList');
		$hooks->registerHandler('register', 'menu:river', '\ColdTrick\EventManager\Menus::stripEventRelationshipRiverMenuItems', 99999);
		$hooks->registerHandler('register', 'menu:event_attendees', '\ColdTrick\EventManager\Menus::registerEventAttendees');
		$hooks->registerHandler('setting', 'plugin', '\ColdTrick\EventManager\Settings::clearCache');
		$hooks->registerHandler('supported_types', 'entity_tools', '\ColdTrick\EventManager\MigrateEvents::supportedSubtypes');
		$hooks->registerHandler('view_vars', 'widgets/content_by_tag/display/simple', '\ColdTrick\EventManager\Widgets::contentByTagEntityTimestamp');
		$hooks->registerHandler('view_vars', 'widgets/content_by_tag/display/slim', '\ColdTrick\EventManager\Widgets::contentByTagEntityTimestamp');
		$hooks->registerHandler('view_vars', 'input/objectpicker/item', '\ColdTrick\EventManager\ObjectPicker::customText');
	}
}
