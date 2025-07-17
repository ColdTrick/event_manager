<?php

namespace ColdTrick\EventManager\Plugins;

/**
 * Support for post_as plugin
 */
class PostAs {
	
	/**
	 * Register post_as support for events
	 *
	 * @param \Elgg\Event $event 'config', 'post_as'
	 *
	 * @return array
	 */
	public static function addConfig(\Elgg\Event $event): array {
		$result = $event->getValue();
		
		$result['event_manager/event/edit'] = [
			'type' => 'object',
			'subtype' => \Event::SUBTYPE,
			'extend_form' => false,
		];
		
		return $result;
	}
}
