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
	
	/**
	 * Checks if plugin setting allows users to write to a container
	 *
	 * @param \Elgg\Event $elgg_event 'container_logic_check', 'object'
	 *
	 * @return void|false
	 */
	public static function containerLogicCheck(\Elgg\Event $elgg_event) {
		if ($elgg_event->getParam('subtype') !== 'event') {
			return;
		}
		
		$container = $elgg_event->getParam('container');
		if ($container instanceof \ElggGroup) {
			$who_create_group_events = elgg_get_plugin_setting('who_create_group_events', 'event_manager'); // group_admin, members
			if (empty($who_create_group_events)) {
				// no one can create
				return false;
			}
			
			$user = $elgg_event->getUserParam();
			$user_guid = $user instanceof \ElggUser ? $user->guid : 0;
			if ($who_create_group_events === 'group_admin' && !$container->canEdit($user_guid)) {
				return false;
			}
			
			// in other group case let regular checks take place
		} else {
			$who_create_site_events = elgg_get_plugin_setting('who_create_site_events', 'event_manager');
			if ($who_create_site_events === 'admin_only' && !elgg_is_admin_logged_in()) {
				return false;
			}
		}
	}
	
	/**
	 * Prevent comments on Events when comments are disabled
	 *
	 * @param \Elgg\Event $event 'container_logic_check', 'object'
	 *
	 * @return bool|null
	 */
	public static function preventEventCommentsWhenDisabled(\Elgg\Event $event): ?bool {
		if ($event->getParam('subtype') !== 'comment') {
			return null;
		}
		
		$container = $event->getParam('container');
		if (!$container instanceof \Event) {
			return null;
		}
		
		if (!$container->comments_on) {
			return false;
		}
		
		return null;
	}
}
