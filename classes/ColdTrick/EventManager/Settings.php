<?php

namespace ColdTrick\EventManager;

class Settings {

	/**
	 * Flushes simple cache after saving the settings
	 *
	 * @param string $hook        hook name
	 * @param string $entity_type hook type
	 * @param bool   $returnvalue current return value
	 * @param array  $params      parameters
	 *
	 * @return bool
	 */
	public static function clearCache($hook, $entity_type, $returnvalue, $params) {
		$plugin = elgg_extract('plugin', $params);
		if (empty($plugin)) {
			return;
		}
		
		if ($plugin->getID() !== 'event_manager') {
			return;
		}
		
		elgg_invalidate_simplecache();
	}
}
