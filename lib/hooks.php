<?php
/**
 * Hook are bundled here
 */


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
function event_manager_invalidate_cache($hook, $entity_type, $returnvalue, $params) {
	$plugin = elgg_extract('plugin', $params);
	if (empty($plugin)) {
		return;
	}
	
	if ($plugin->getID() !== 'event_manager') {
		return;
	}
	
	elgg_invalidate_simplecache();
}
