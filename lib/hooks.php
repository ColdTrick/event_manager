<?php
/**
 * Hook are bundled here
 */

/**
 * Allow non user to remove their registration correctly
 *
 * @param string $hook        hook name
 * @param string $entity_type hook type
 * @param bool   $returnvalue current return value
 * @param array  $params      parameters
 *
 * @return bool
 */
function event_manager_permissions_check_handler($hook, $entity_type, $returnvalue, $params) {
	global $EVENT_MANAGER_UNDO_REGISTRATION;
	$result = $returnvalue;

	// only override the hook if not already allowed
	if (!$result && !empty($params) && is_array($params)) {
		$entity = elgg_extract("entity", $params);

		if (elgg_instanceof($entity, "object", EventRegistration::SUBTYPE)) {
			if (!empty($EVENT_MANAGER_UNDO_REGISTRATION)) {
				$result = true;
			}
		}
	}

	return $result;
}

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
