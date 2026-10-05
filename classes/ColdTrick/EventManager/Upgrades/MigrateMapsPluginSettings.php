<?php

namespace ColdTrick\EventManager\Upgrades;

use Elgg\Upgrade\Result;
use Elgg\Upgrade\SystemUpgrade;

/**
 * Migrates the maps plugin settings
 */
class MigrateMapsPluginSettings extends SystemUpgrade {
	
	/**
	 * {@inheritdoc}
	 */
	public function getVersion(): int {
		return 2026100201;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function shouldBeSkipped(): bool {
		return false;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function needsIncrementOffset(): bool {
		return false;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function countItems(): int {
		return 1;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function run(Result $result, $offset): Result {
		$plugin = elgg_get_plugin_from_id('event_manager');

		if ($plugin->maps_provider === 'osm') {
			$plugin->maps_enabled = true;
		}

		$plugin->maps_location = $plugin->osm_default_location;
		$plugin->maps_location_lat = $plugin->osm_default_location_lat;
		$plugin->maps_location_lng = $plugin->osm_default_location_lng;
		$plugin->maps_zoom = $plugin->osm_default_zoom;
		$plugin->maps_detail_zoom = $plugin->osm_detail_zoom;
				
		$plugin->unsetSetting('maps_provider');
		$plugin->unsetSetting('osm_default_location');
		$plugin->unsetSetting('osm_default_location_lat');
		$plugin->unsetSetting('osm_default_location_lng');
		$plugin->unsetSetting('osm_default_zoom');
		$plugin->unsetSetting('osm_detail_zoom');
			
		$result->markComplete();
		
		return $result;
	}
}
