<?php

namespace ColdTrick\EventManager;

class Views {
	
	/**
	 * Loads leaflet css if needed
	 *
	 * @param \Elgg\Hook $hook 'view_vars', 'event_manager/onthemap'
	 *
	 * @return void
	 */
	public static function loadLeafletCss(\Elgg\Hook $hook) {
		$maps_provider = elgg_get_plugin_setting('maps_provider', 'event_manager', 'google');
		if ($maps_provider !== 'osm') {
			return;
		}
		
		elgg_load_external_file('css', 'leafletjs');
	}
}
