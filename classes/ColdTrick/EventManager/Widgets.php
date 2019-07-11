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
		
		$widget = elgg_extract('entity', $params);
		if (!empty($returnvalue) || !($widget instanceof \ElggWidget) || $widget->handler !== 'events') {
			return;
		}
			
		switch ($widget->context) {
			case 'index':
				if ($widget->event_status === 'live') {
					return elgg_generate_url('collection:object:event:live');
				}
				
				return elgg_generate_url('collection:object:event:upcoming');
			case 'groups':
				if ($widget->event_status === 'live') {
					return elgg_generate_url('collection:object:event:live', ['guid' => $widget->getOwnerGUID()]);
				}
				
				return elgg_generate_url('collection:object:event:group', ['guid' => $widget->getOwnerGUID()]);
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
		if (!$container instanceof \ElggGroup) {
			return;
		}
		
		if ($container->isToolEnabled('event_manager')) {
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
	
	/**
	 * Change the entity_timestamp in the content_by_tag widget to show the start date of the event
	 *
	 * @param string $hook        hook name
	 * @param string $entity_type hook type
	 * @param array  $returnvalue current return value
	 * @param array  $params      parameters
	 *
	 * @return void|array
	 */
	public static function contentByTagEntityTimestamp($hook, $entity_type, $returnvalue, $params) {
		
		$entity = elgg_extract('entity', $returnvalue);
		if (!($entity instanceof \Event)) {
			return;
		}
		
		$returnvalue['entity_timestamp'] = $entity->getStartTimestamp();
		
		return $returnvalue;
	}
}
