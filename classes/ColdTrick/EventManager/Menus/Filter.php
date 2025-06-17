<?php

namespace ColdTrick\EventManager\Menus;

use Elgg\Menu\MenuItems;

/**
 * Filter menu related callbacks
 */
class Filter {
	
	/**
	 * Add filter tabs for event lists
	 *
	 * @param \Elgg\Event $event 'register', 'menu:filter:events'
	 *
	 * @return MenuItems
	 */
	public static function registerEventsList(\Elgg\Event $event): MenuItems {
		$route_params = [
			'list_type' => get_input('list_type'),
			'tag' => get_input('tag'),
		];
		
		$page_owner = elgg_get_page_owner_entity();
		if ($page_owner instanceof \ElggGroup) {
			$route_params['guid'] = $page_owner->guid;
		}
		
		$selected = $event->getParam('filter_value');
		
		$result = $event->getValue();
		
		$result[] = \ElggMenuItem::factory([
			'name' => 'live',
			'text' => elgg_echo('event_manager:list:navigation:live'),
			'href' => elgg_generate_url('collection:object:event:live', $route_params),
			'rel' => 'list',
			'selected' => $selected === 'live',
			'priority' => 100,
		]);
		
		$result[] = \ElggMenuItem::factory([
			'name' => 'upcoming',
			'text' => elgg_echo('event_manager:list:navigation:upcoming'),
			'href' => elgg_generate_url('collection:object:event:upcoming', $route_params),
			'rel' => 'list',
			'selected' => $selected === 'upcoming',
			'priority' => 200,
		]);
		
		// user links (not in group context)
		if (!$page_owner instanceof \ElggGroup && elgg_is_logged_in()) {
			$result[] = \ElggMenuItem::factory([
				'name' => 'attending',
				'text' => elgg_echo('event_manager:menu:attending'),
				'href' => elgg_generate_url('collection:object:event:attending', [
					'username' => elgg_get_logged_in_user_entity()->username,
					'list_type' => get_input('list_type'),
					'tag' => get_input('tag'),
				]),
				'selected' => $selected === 'attending',
				'priority' => 300,
			]);
			
			$result[] = \ElggMenuItem::factory([
				'name' => 'mine',
				'text' => elgg_echo('mine'),
				'href' => elgg_generate_url('collection:object:event:owner', [
					'username' => elgg_get_logged_in_user_entity()->username,
					'list_type' => get_input('list_type'),
					'tag' => get_input('tag'),
				]),
				'selected' => $selected === 'mine',
				'priority' => 400,
			]);
		}
		
		return $result;
	}
	
	/**
	 * Add view types (listing/calendar/map) to the filter tabs
	 *
	 * @param \Elgg\Event $event 'register', 'menu:filter:events'
	 *
	 * @return MenuItems
	 */
	public static function registerViewTypes(\Elgg\Event $event): MenuItems {
		
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
