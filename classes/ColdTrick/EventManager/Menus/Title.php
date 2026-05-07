<?php

namespace ColdTrick\EventManager\Menus;

use Elgg\Menu\MenuItems;

/**
 * Site menu related callbacks
 */
class Title {
	
	/**
	 * Adds items to the site menu
	 *
	 * @param \Elgg\Event $event 'register', 'menu:site'
	 *
	 * @return MenuItems
	 */
	public static function registerICal(\Elgg\Event $event): MenuItems {
		$result = $event->getValue();

		if ($event->getParam('identifier') !== 'event' || $event->getParam('filter_id') !== 'events') {
			return $result;
		}

		if (elgg_get_plugin_setting('ical_direct', 'event_manager')) {
			$result[] = \ElggMenuItem::factory([
				'name' => 'ical',
				'icon' => 'download',
				'text' => elgg_echo('event_manager:ical_direct'),
				'href' => false,
				'child_menu' => [
					'display' => 'dropdown',
				],
				'data-position' => json_encode([
					'my' => 'right top',
					'at' => 'right bottom',
				]),
				'link_class' => [
					'elgg-button',
					'elgg-button-action',
				],
			]);
			
			$result[] = \ElggMenuItem::factory([
				'name' => 'export-ical',
				'icon' => 'download',
				'text' => elgg_echo('event_manager:ical_direct:export'),
				'href' => elgg_http_add_url_query_elements('ajax/form/event_manager/export/ical', [
					'list_route' => elgg_get_current_route_name(),
					'route_parameters' => elgg_get_current_route()->getMatchedParameters(),
				]),
				'link_class' => 'elgg-lightbox',
				'parent_name' => 'ical',
			]);
			
			$result[] = \ElggMenuItem::factory([
				'name' => 'import-ical',
				'icon' => 'upload',
				'text' => elgg_echo('event_manager:ical_direct:import'),
				'href' => elgg_http_add_url_query_elements('ajax/form/event_manager/import/ical', [
					'list_route' => elgg_get_current_route_name(),
					'route_parameters' => elgg_get_current_route()->getMatchedParameters(),
				]),
				'link_class' => 'elgg-lightbox',
				'parent_name' => 'ical',
			]);
		}
		
		return $result;
	}
}
