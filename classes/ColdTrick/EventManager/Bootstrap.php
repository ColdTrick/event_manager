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
			'name' => 'event',
			'icon' => 'calendar-alt',
			'text' => elgg_echo('event_manager:menu:title'),
			'href' => elgg_generate_url('default:object:event'),
		]);
	
		// add group tool option
		if (elgg_get_plugin_setting('who_create_group_events', 'event_manager')) {
			elgg()->group_tools->register('event_manager', ['label' => elgg_echo('groups:enableevents')]);
		}
		
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
		
		// register js libraries
		elgg_define_js('gmaps', [
			'src' => elgg_get_simplecache_url('js/hpneo/gmaps/gmaps.js'),
		]);
		elgg_define_js('event_manager/maps', ['src' => elgg_get_simplecache_url('js/event_manager/maps.js')]);
	
		// leafletjs
		elgg_define_js('leafletjs', [
			'src' => '//unpkg.com/leaflet@1.2.0/dist/leaflet.js',
		]);
		elgg_register_external_file('css', 'leafletjs', '//unpkg.com/leaflet@1.2.0/dist/leaflet.css', 'head');
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
		
		$hooks->registerHandler('container_logic_check', 'object', '\ColdTrick\EventManager\Access::containerLogicCheck');
		$hooks->registerHandler('elgg.data', 'site', '\ColdTrick\EventManager\Js::getJsConfig');
		$hooks->registerHandler('entity:url', 'object', '\ColdTrick\EventManager\Widgets::getEventsUrl');
		$hooks->registerHandler('entity:icon:sizes', 'object', '\ColdTrick\EventManager\Icons::getIconSizes');
		$hooks->registerHandler('entity:icon:file', 'object', '\ColdTrick\EventManager\Icons::getIconFile');
		$hooks->registerHandler('entity:icon:url', 'object', '\ColdTrick\EventManager\Icons::getEventRegistrationIconURL');
		$hooks->registerHandler('export_attendee', 'event', '\ColdTrick\EventManager\Attendees::exportBaseAttributes', 100);
		$hooks->registerHandler('export_attendee', 'event', '\ColdTrick\EventManager\Attendees::exportQuestionData', 200);
		$hooks->registerHandler('export_attendee', 'event', '\ColdTrick\EventManager\Attendees::exportProgramData', 300);
		$hooks->registerHandler('export:metadata_names', 'elasticsearch', '\ColdTrick\EventManager\Elasticsearch::exportMetadataNames');
		$hooks->registerHandler('handlers', 'widgets', '\ColdTrick\EventManager\Widgets::registerHandlers');
		$hooks->registerHandler('likes:is_likable', 'object:' . \Event::SUBTYPE, '\Elgg\Values::getTrue');
		$hooks->registerHandler('prepare', 'notification:create:object:' . Event::SUBTYPE, '\ColdTrick\EventManager\Notifications::prepareCreateEventNotification');
		$hooks->registerHandler('prepare', 'system:email', '\ColdTrick\EventManager\Notifications::prepareEventRegistrationSender');
		$hooks->registerHandler('register', 'menu:filter:events', '\ColdTrick\EventManager\Menus::registerEventsList');
		$hooks->registerHandler('register', 'menu:entity', '\ColdTrick\EventManager\Menus::registerAttendeeActions');
		$hooks->registerHandler('register', 'menu:entity', '\ColdTrick\EventManager\Menus::registerEntity', 600);
		$hooks->registerHandler('register', 'menu:event:rsvp', '\ColdTrick\EventManager\Menus::registerRsvp');
		$hooks->registerHandler('register', 'menu:owner_block', '\ColdTrick\EventManager\Menus::registerGroupOwnerBlock');
		$hooks->registerHandler('register', 'menu:owner_block', '\ColdTrick\EventManager\Menus::registerUserOwnerBlock');
		$hooks->registerHandler('register', 'menu:event_files', '\ColdTrick\EventManager\Menus::registerEventFiles');
		$hooks->registerHandler('register', 'menu:river', '\ColdTrick\EventManager\Menus::stripEventRelationshipRiverMenuItems', 99999);
		$hooks->registerHandler('register', 'menu:event_attendees', '\ColdTrick\EventManager\Menus::registerEventAttendees');
		$hooks->registerHandler('search:fields', 'object:event', '\ColdTrick\EventManager\Search::addFields');
		$hooks->registerHandler('setting', 'plugin', '\ColdTrick\EventManager\Settings::clearCache');
		$hooks->registerHandler('supported_types', 'entity_tools', '\ColdTrick\EventManager\MigrateEvents::supportedSubtypes');
		$hooks->registerHandler('view_vars', 'event_manager/onthemap', '\ColdTrick\EventManager\Views::loadLeafletCss');
		$hooks->registerHandler('view_vars', 'widgets/content_by_tag/display/simple', '\ColdTrick\EventManager\Widgets::contentByTagEntityTimestamp');
		$hooks->registerHandler('view_vars', 'widgets/content_by_tag/display/slim', '\ColdTrick\EventManager\Widgets::contentByTagEntityTimestamp');
		$hooks->registerHandler('view_vars', 'input/objectpicker/item', '\ColdTrick\EventManager\ObjectPicker::customText');
	}
}
