<?php

namespace ColdTrick\EventManager;

/**
 * Widget related callbacks
 */
class Widgets {
	
	/**
	 * Generates correct title link for widgets depending on the context
	 *
	 * @param \Elgg\Event $event 'entity:url', 'object'
	 *
	 * @return null|string
	 */
	public static function getEventsUrl(\Elgg\Event $event): ?string {
		
		$widget = $event->getEntityParam();
		if (!empty($event->getValue()) || !$widget instanceof \ElggWidget || $widget->handler !== 'events') {
			return null;
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
		
		return null;
	}

	/**
	 * Registers the widget handlers for events
	 *
	 * @param \Elgg\Event $event 'handlers', 'widgets'
	 *
	 * @return null|array
	 */
	public static function registerHandlers(\Elgg\Event $event): ?array {
		
		$container = $event->getParam('container');
		if (!$container instanceof \ElggGroup) {
			return null;
		}
		
		if ($container->isToolEnabled('event_manager')) {
			return null;
		}
		
		$result = $event->getValue();
		foreach ($result as $index => $widget) {
			if ($widget->id === 'events') {
				unset($result[$index]);
				return $result;
			}
		}

		return $result;
	}
	
	/**
	 * Change the entity_timestamp in the content_by_tag widget to show the start date of the event
	 *
	 * @param \Elgg\Event $event 'view_vars', 'widgets/content_by_tag/display/[simple|slim]'
	 *
	 * @return null|array
	 */
	public static function contentByTagEntityTimestamp(\Elgg\Event $event): ?array {
		$vars = $event->getValue();
		
		$entity = elgg_extract('entity', $vars);
		if (!$entity instanceof \Event) {
			return null;
		}
		
		$vars['entity_timestamp'] = $entity->getStartTimestamp();
		
		return $vars;
	}
}
