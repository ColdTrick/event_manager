<?php

namespace ColdTrick\EventManager;

/**
 * Icon related callbacks
 */
class Icons {
		
	/**
	 * Returns the user default icon for event registration objects
	 *
	 * @param \Elgg\Event $event 'entity:icon:url', 'object'
	 *
	 * @return null|string
	 */
	public static function getEventRegistrationIconURL(\Elgg\Event $event): ?string {
		
		$entity = $event->getEntityParam();
		if (!$entity instanceof \EventRegistration) {
			return null;
		}
		
		$type = $event->getParam('type', 'icon');
		$size = $event->getParam('size', 'medium');
		
		$entity_type = 'user';
		$entity_subtype = 'user';

		$exts = ['svg', 'gif', 'png', 'jpg'];

		foreach ($exts as $ext) {
			foreach ([$entity_subtype, 'default'] as $subtype) {
				if ($ext == 'svg' && elgg_view_exists("{$type}/{$entity_type}/{$subtype}.svg")) {
					return elgg_get_simplecache_url("{$type}/{$entity_type}/{$subtype}.svg");
				}
				
				if (elgg_view_exists("{$type}/{$entity_type}/{$subtype}/{$size}.{$ext}")) {
					return elgg_get_simplecache_url("{$type}/{$entity_type}/{$subtype}/{$size}.{$ext}");
				}
			}
		}

		if (!elgg_view_exists("{$type}/default/{$size}.png")) {
			return null;
		}
		
		return elgg_get_simplecache_url("{$type}/default/{$size}.png");
	}
}
