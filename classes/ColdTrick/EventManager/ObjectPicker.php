<?php

namespace ColdTrick\EventManager;

/**
 * Objectpicker related callbacks
 */
class ObjectPicker {
	
	/**
	 * Adds custom text for the objectpicker
	 *
	 * @param \Elgg\Event $elgg_event 'view_vars', 'input/objectpicker/item'
	 *
	 * @return null|array
	 */
	public static function customText(\Elgg\Event $elgg_event): ?array {
		$vars = $elgg_event->getValue();
		$entity = elgg_extract('entity', $vars);
		if (!$entity instanceof \Event) {
			return null;
		}
		
		$text = $entity->getDisplayName() . ' (' . event_manager_format_date($entity->getStartTimestamp()) . ')';
		$text .= elgg_format_element('div', ['class' => 'elgg-subtext'], $entity->getExcerpt(200));
		
		$vars['text'] = $text;
		
		return $vars;
	}
}
