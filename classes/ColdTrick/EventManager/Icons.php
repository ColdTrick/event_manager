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
}