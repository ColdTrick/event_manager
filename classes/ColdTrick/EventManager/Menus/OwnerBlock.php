<?php

namespace ColdTrick\EventManager\Menus;

use Elgg\Menu\MenuItems;

/**
 * Owner Block menu related callbacks
 */
class OwnerBlock {
	
	/**
	 * add menu item for groups to owner block
	 *
	 * @param \Elgg\Event $event 'register', 'menu:owner_block'
	 *
	 * @return null|MenuItems
	 */
	public static function registerGroup(\Elgg\Event $event): ?MenuItems {
		
		$group = $event->getEntityParam();
		if (!$group instanceof \ElggGroup) {
			return null;
		}
		
		if (!$group->canWriteToContainer(0, 'object', 'event') || !$group->isToolEnabled('event_manager')) {
			return null;
		}
		
		$result = $event->getValue();
		
		$result[] = \ElggMenuItem::factory([
			'name' => 'events',
			'text' => elgg_echo('event_manager:menu:group_events'),
			'href' => elgg_generate_url('collection:object:event:group', ['guid' => $group->guid]),
		]);
		
		return $result;
	}
	
	/**
	 * add menu item to user owner block
	 *
	 * @param \Elgg\Event $event 'register', 'menu:owner_block'
	 *
	 * @return null|MenuItems
	 */
	public static function registerUser(\Elgg\Event $event): ?MenuItems {
		
		$user = $event->getEntityParam();
		if (!$user instanceof \ElggUser) {
			return null;
		}
		
		$result = $event->getValue();
		
		$result[] = \ElggMenuItem::factory([
			'name' => 'events',
			'text' => elgg_echo('item:object:event'),
			'href' => elgg_generate_url('collection:object:event:owner', ['username' => $user->username]),
		]);
		
		return $result;
	}
}
