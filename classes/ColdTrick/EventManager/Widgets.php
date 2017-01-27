<?php

namespace ColdTrick\EventManager;

class Widgets {
	/**
	 * Generates correct title link for widgets depending on the context
	 *
	 * @param string $hook        hook name
	 * @param string $entity_type hook type
	 * @param array  $returnvalue current return value
	 * @param array  $params      parameters
	 *
	 * @return string
	 */
	public static function getEventsUrl($hook, $entity_type, $returnvalue, $params) {
		$result = $returnvalue;
		$widget = elgg_extract('entity', $params);
	
		if (empty($result) || !($widget instanceof ElggWidget) || $widget->handler !== 'events') {
			return;
		}
			
		switch ($widget->context) {
			case 'index':
				return '/events';
			case 'groups':
				return '/events/event/list/' . $widget->getOwnerGUID();
		}
	}

	/**
	 * Registers the widget handlers for events
	 *
	 * @param string $hook        hook name
	 * @param string $entity_type hook type
	 * @param array  $returnvalue current return value
	 * @param array  $params      parameters
	 *
	 * @return string
	 */
	public static function registerHandlers($hook, $entity_type, $returnvalue, $params) {
		
		$container = elgg_extract('container', $params);
		if (!($container instanceof \ElggGroup)) {
			return;
		}
		
		if ($container->event_manager_enable !== 'no') {
			return;
		}
		
		foreach ($returnvalue as $index => $widget) {
			if ($widget->id === 'events') {
				unset($returnvalue[$index]);
				return $returnvalue;
			}
		}

		return $returnvalue;
	}
}