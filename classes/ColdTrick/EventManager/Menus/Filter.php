<?php

namespace ColdTrick\EventManager\Menus;

use Elgg\Menu\MenuItems;

/**
 * Filter menu related callbacks
 */
class Filter {
	
	/**
	 * Add view types (listing/calendar/map) to the filter tabs
	 *
	 * @param \Elgg\Event $event 'register', 'menu:filter:events'
	 *
	 * @return MenuItems
	 */
	public static function registerViewTypes(\Elgg\Event $event) {
		
		/* @var $items MenuItems */
		$items = $event->getValue();
		
		$list_type = get_input('list_type', 'list');
		
		$parent_name = "event-manager-{$list_type}";
		
		$items[] = \ElggMenuItem::factory([
			'name' => 'event-manager-list',
			'icon' => 'list',
			'text' => elgg_echo('viewtype:list'),
			'href' => elgg_http_add_url_query_elements(elgg_get_current_url(), [
				'list_type' => 'list',
				'limit' => null,
				'offset' => null,
			]),
			'parent_name' => $parent_name,
			'priority' => 100,
			'selected' => false,
		]);
		
		$items[] = \ElggMenuItem::factory([
			'name' => 'event-manager-calendar',
			'icon' => 'calendar-alt',
			'text' => elgg_echo('event_manager:list:navigation:calendar'),
			'href' => elgg_http_add_url_query_elements(elgg_get_current_url(), [
				'list_type' => 'calendar',
				'limit' => null,
				'offset' => null,
			]),
			'parent_name' => $parent_name,
			'priority' => 200,
			'selected' => false,
		]);
		
		if (event_manager_get_maps_provider() !== 'none') {
			$items[] = \ElggMenuItem::factory([
				'name' => 'event-manager-map',
				'icon' => 'map-marked-alt',
				'text' => elgg_echo('event_manager:list:navigation:onthemap'),
				'href' => elgg_http_add_url_query_elements(elgg_get_current_url(), [
					'list_type' => 'map',
					'limit' => null,
					'offset' => null,
				]),
				'parent_name' => $parent_name,
				'priority' => 300,
				'selected' => false,
			]);
		}
		
		/** @var $parent_item \ElggMenuItem */
		$parent_item = $items->get($parent_name);
		$parent_item->setHref(false);
		$parent_item->setParentName(false);
		$parent_item->setPriority(99999);
		$parent_item->setItemClass('event-manager-listing-sorting');
		$parent_item->setChildMenuOptions([
			'display' => 'dropdown',
			'data-position' => json_encode([
				'my' => 'right top',
				'at' => 'right bottom',
				'collision' => 'fit fit',
			]),
		]);
		
		return $items;
	}
}
