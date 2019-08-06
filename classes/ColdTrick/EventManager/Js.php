<?php

namespace ColdTrick\EventManager;

class Js {
	
	/**
	 * Returns elgg.data config
	 *
	 * @param string \Elgg\Hook $hook 'elgg.data', 'site'
	 *
	 * @return array
	 */
	public static function getJsConfig(\Elgg\Hook $hook) {
		$result = $hook->getValue();
		
		$result['event_manager_osm_default_zoom'] = elgg_get_plugin_setting('osm_default_zoom', 'event_manager');
		$result['event_manager_osm_default_location_lat'] = elgg_get_plugin_setting('osm_default_location_lat', 'event_manager');
		$result['event_manager_osm_default_location_lng'] = elgg_get_plugin_setting('osm_default_location_lng', 'event_manager');
		
		return $result;
	}
}
