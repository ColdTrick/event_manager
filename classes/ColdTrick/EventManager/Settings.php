<?php

namespace ColdTrick\EventManager;

class Settings {

	/**
	 * Flushes simple cache after saving the settings
	 *
	 * @param \Elgg\Hook $hook 'setting', 'plugin'
	 *
	 * @return bool
	 */
	public static function clearCache(\Elgg\Hook $hook) {
		$plugin = $hook->getParam('plugin');
		if (empty($plugin)) {
			return;
		}
		
		if ($plugin->getID() !== 'event_manager') {
			return;
		}
		
		elgg_invalidate_simplecache();
	}
}
