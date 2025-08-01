<?php

namespace ColdTrick\EventManager;

use Elgg\DefaultPluginBootstrap;

/**
 * Plugin bootstrap
 */
class Bootstrap extends DefaultPluginBootstrap {
	
	/**
	 * {@inheritdoc}
	 */
	public function init() {
	
		// add group tool option
		if (elgg_get_plugin_setting('who_create_group_events', 'event_manager')) {
			elgg()->group_tools->register('event_manager');
		}
				
		if (elgg_is_active_plugin('entity_tools')) {
			elgg_register_event_handler('supported_types', 'entity_tools', '\ColdTrick\EventManager\MigrateEvents::supportedSubtypes');
		}
		
		if (elgg_is_active_plugin('post_as')) {
			elgg_extend_view('forms/event_manager/event/tabs/profile', 'post_as/input');
		}
		
		// leafletjs
		elgg_register_esm('leafletjs', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js');
		elgg_register_external_file('css', 'leafletjs', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', 'head');
	}
}
