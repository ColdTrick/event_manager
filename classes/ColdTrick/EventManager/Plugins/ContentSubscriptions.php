<?php

namespace ColdTrick\EventManager\Plugins;

class ContentSubscriptions {
	
	/**
	 * Add the event subtype to the content subscriptions allowed types
	 *
	 * @param \Elgg\Hook $hook 'entity_types', 'content_subscriptions'
	 *
	 * @return array
	 */
	public static function registerContentType(\Elgg\Hook $hook) {
		$result = $hook->getValue();
		
		$result['object'][] = \Event::SUBTYPE;
		
		return $result;
	}
}
