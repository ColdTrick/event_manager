<?php

namespace ColdTrick\EventManager;

class Widgets {
	
	/**
	 * Generates correct title link for widgets depending on the context
	 *
	 * @param \Elgg\Hook $hook 'entity:url', 'object'
	 *
	 * @return string
	 */
	public static function getEventsUrl(\Elgg\Hook $hook) {
		
		$widget = $hook->getEntityParam();
		if (!empty($hook->getValue()) || !($widget instanceof \ElggWidget) || $widget->handler !== 'events') {
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
	 * @param \Elgg\Hook $hook 'handlers', 'widgets'
	 *
	 * @return string
	 */
	public static function registerHandlers(\Elgg\Hook $hook) {
		
		$container = $hook->getParam('container');
		if (!$container instanceof \ElggGroup) {
			return;
		}
		
		if ($container->isToolEnabled('event_manager')) {
			return;
		}
		
		$returnvalue = $hook->getValue();
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
	 * @param \Elgg\Hook $hook 'view_vars', 'widgets/content_by_tag/display/[simple|slim]'
	 *
	 * @return void|array
	 */
	public static function contentByTagEntityTimestamp(\Elgg\Hook $hook) {
		$vars = $hook->getValue();
		
		$entity = elgg_extract('entity', $vars);
		if (!$entity instanceof \Event) {
			return;
		}
		
		$vars['entity_timestamp'] = $entity->getStartTimestamp();
		
		return $vars;
	}
}
