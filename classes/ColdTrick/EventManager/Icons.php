<?php

namespace ColdTrick\EventManager;

class Icons {
	
	/**
	 * Returns additional icon sizes for Events
	 *
	 * @param string $hook        hook name
	 * @param string $entity_type hook type
	 * @param array  $returnvalue current return value
	 * @param array  $params      parameters
	 *
	 * @return array
	 *
	 * @see elgg_get_icon_sizes()
	 */
	public static function getIconSizes($hook, $entity_type, $returnvalue, $params) {
		$subtype = elgg_extract('entity_subtype', $params);
		if ($subtype !== 'event') {
			return;
		}
		
		$returnvalue['event_banner'] = [
			'w' => 1920,
			'h' => 1080,
			'square' => false,
			'upscale' => false,
		];
		return $returnvalue;
	}
	
	/**
	 * Set correct filename for Event icon
	 *
	 * @param string    $hook        hook name
	 * @param string    $entity_type hook type
	 * @param \ElggIcon $returnvalue current return value
	 * @param array     $params      parameters
	 *
	 * @return void|\ElggIcon
	 */
	public static function getIconFile($hook, $entity_type, $returnvalue, $params) {
		
		$entity = elgg_extract('entity', $params);
		if (!($entity instanceof \Event)) {
			return;
		}
		
		$size = elgg_extract('size', $params);
		$returnvalue->setFilename("{$size}.jpg");
		
		return $returnvalue;
	}
		
	/**
	 * Returns the user default icon for event registration objects
	 *
	 * @param \Elgg\Hook $hook 'entity:icon:url', 'object'
	 *
	 * @return string
	 */
	public static function getEventRegistrationIconURL(\Elgg\Hook $hook) {
		
		$entity = $hook->getEntityParam();
		if (!$entity instanceof \EventRegistration) {
			return;
		}
		
		$type = $hook->getParam('type', 'icon');
		$size = $hook->getParam('size', 'medium');
		
		$entity_type = 'user';
		$entity_subtype = 'user';

		$exts = ['svg', 'gif', 'png', 'jpg'];

		foreach ($exts as $ext) {
			foreach ([$entity_subtype, 'default'] as $subtype) {
				if ($ext == 'svg' && elgg_view_exists("$type/$entity_type/$subtype.svg")) {
					return elgg_get_simplecache_url("$type/$entity_type/$subtype.svg");
				}
				if (elgg_view_exists("$type/$entity_type/$subtype/$size.$ext")) {
					return elgg_get_simplecache_url("$type/$entity_type/$subtype/$size.$ext");
				}
			}
		}

		if (elgg_view_exists("$type/default/$size.png")) {
			return elgg_get_simplecache_url("$type/default/$size.png");
		}
	}
}