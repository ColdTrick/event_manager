<?php

namespace ColdTrick\EventManager;

class Search {
	
	/**
	 * Add searchable fields for events
	 *
	 * @param \Elgg\Hook $hook 'search:fields', 'object:event'
	 *
	 * @return array
	 */
	public static function addFields(\Elgg\Hook $hook) {
		
		$value = (array) $hook->getValue();
		
		$defaults = [
			'metadata' => [],
		];
		
		$value = array_merge($defaults, $value);
		
		$fields = [
			'event_type',
			'location',
			'region',
			'shortdescription',
		];
		
		$value['metadata'] = array_merge($value['metadata'], $fields);
		
		return $value;
	}
}
