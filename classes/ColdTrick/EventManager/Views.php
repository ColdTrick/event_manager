<?php

namespace ColdTrick\EventManager;

/**
 * Views related callbacks
 */
class Views {
	
	/**
	 * Loads leaflet css if needed
	 *
	 * @param \Elgg\Event $event 'view_vars', 'event_manager/listing/map'
	 *
	 * @return void
	 */
	public static function loadLeafletCss(\Elgg\Event $event) {
		if (event_manager_get_maps_provider() !== 'osm') {
			return;
		}
		
		elgg_load_external_file('css', 'leafletjs');
	}
}
