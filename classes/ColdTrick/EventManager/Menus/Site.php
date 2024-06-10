<?php

namespace ColdTrick\EventManager\Menus;

use Elgg\Menu\MenuItems;

/**
 * Site menu related callbacks
 */
class Site {
	
	/**
	 * Adds items to the site menu
	 *
	 * @param \Elgg\Event $event 'register', 'menu:site'
	 *
	 * @return MenuItems
	 */
	public static function registerEvents(\Elgg\Event $event): MenuItems {
		
		$result = $event->getValue();
		
		$result[] = \ElggMenuItem::factory([
			'name' => 'event',
			'icon' => 'calendar-alt',
			'text' => elgg_echo('event_manager:menu:title'),
			'href' => elgg_generate_url('default:object:event'),
		]);
		
		return $result;
	}
}
