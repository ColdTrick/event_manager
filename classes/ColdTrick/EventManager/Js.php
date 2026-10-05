<?php

namespace ColdTrick\EventManager;

/**
 * Javascript related callbacks
 */
class Js {
	
	/**
	 * Returns elgg.data config
	 *
	 * @param string \Elgg\Event $event 'elgg.data', 'page'
	 *
	 * @return array
	 */
	public static function getJsConfig(\Elgg\Event $event): array {
		$result = $event->getValue();
		
		$result['event_manager_maps_zoom'] = elgg_get_plugin_setting('maps_zoom', 'event_manager');
		$result['event_manager_maps_location_lat'] = elgg_get_plugin_setting('maps_location_lat', 'event_manager');
		$result['event_manager_maps_location_lng'] = elgg_get_plugin_setting('maps_location_lng', 'event_manager');
		
		return $result;
	}
}
