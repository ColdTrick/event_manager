<?php

namespace ColdTrick\EventManager;

/**
 * Event related special permissions
 */
class Permissions {
	
	/**
	 * Gives extra rights to event organizers
	 *
	 * @param \Elgg\Event $event 'permissions_check', 'object'
	 *
	 * @return null|bool
	 */
	public static function organizersCanEdit(\Elgg\Event $event): ?bool {
		if ($event->getValue()) {
			// already true, leave as is
			return null;
		}
		
		$entity = $event->getEntityParam();
		if (!$entity instanceof \Event) {
			return null;
		}

		return in_array(elgg_get_logged_in_user_guid(), (array) $entity->organizer_guids);
	}
}
