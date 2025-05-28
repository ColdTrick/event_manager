<?php

namespace ColdTrick\EventManager\Menus;

use Elgg\Menu\MenuItems;

/**
 * Register menu items in the filter:event/attendees menu
 */
class FilterEventAttendees {
	
	/**
	 * Register the event relationship to the attendee listing
	 *
	 * @param \Elgg\Event $event 'register', 'menu:filter:event/attendees'
	 *
	 * @return null|MenuItems
	 */
	public static function registerAttendeeRelationships(\Elgg\Event $event): ?MenuItems {
		$entity = $event->getParam('event_entity');
		if (!$entity instanceof \Event) {
			return null;
		}
		
		$valid_relationships = $entity->getSupportedRelationships();
		if (count($valid_relationships) === 1) {
			return null;
		}
		
		/* @var $result MenuItems */
		$result = $event->getValue();
		
		foreach ($valid_relationships as $rel => $label) {
			$result[] = \ElggMenuItem::factory([
				'name' => $rel,
				'text' => $label,
				'href' => elgg_generate_url('collection:object:event:attendees', [
					'guid' => $entity->guid,
					'relationship' => $rel,
				]),
			]);
		}
		
		return $result;
	}
}
