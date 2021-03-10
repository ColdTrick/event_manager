<?php

namespace ColdTrick\EventManager\Menus;

use Elgg\Menu\MenuItems;

class Entity {
	
	/**
	 * Register mail attendees to the entity menu
	 *
	 * @param \Elgg\Hook $hook 'register', 'menu:entity'
	 *
	 * @return void|MenuItems
	 */
	public static function registerMailAttendees(\Elgg\Hook $hook) {
		
		$entity = $hook->getEntityParam();
		if (!$entity instanceof \Event || !$entity->canEdit()) {
			return;
		}
		
		if (!(bool) elgg_get_plugin_setting('event_mail', 'event_manager')) {
			return;
		}
		
		$result = $hook->getValue();
		
		$result[] = \ElggMenuItem::factory([
			'name' => 'event_mail',
			'icon' => 'envelope',
			'text' => elgg_echo('event_manager:menu:mail'),
			'href' => elgg_generate_entity_url($entity, 'mail'),
		]);
		
		return $result;
	}
}
