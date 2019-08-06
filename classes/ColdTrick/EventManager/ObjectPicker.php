<?php

namespace ColdTrick\EventManager;

class ObjectPicker {
	
	/**
	 * Adds custom text for the objectpicker
	 *
	 * @param \Elgg\Hook $hook 'view_vars', 'input/objectpicker/item'
	 *
	 * @return string
	 */
	public static function customText(\Elgg\Hook $hook) {
		$vars = $hook->getValue();
		$entity = elgg_extract('entity', $vars);
		if (!$entity instanceof \Event) {
			return;
		}
		
		$text = $entity->getDisplayName() . ' (' . event_manager_format_date($entity->getStartTimestamp()) . ')';
		$text .= elgg_format_element('div', ['class' => 'elgg-subtext'], $entity->getExcerpt(200));
		
		$vars['text'] = $text;
		return $vars;
	}
}
