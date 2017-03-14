<?php

namespace ColdTrick\EventManager;

class ObjectPicker {
	
	/**
	 * Adds custom text for the objectpicker
	 *
	 * @param string $hook        hook name
	 * @param string $entity_type hook type
	 * @param array  $returnvalue current return value
	 * @param array  $params      parameters
	 *
	 * @return string
	 */
	public static function customText($hook, $entity_type, $returnvalue, $params) {
		
		$entity = elgg_extract('entity', $returnvalue);
		if (!($entity instanceof \Event)) {
			return;
		}
		
		$text = $entity->getDisplayName() . ' (' . event_manager_format_date($entity->getStartTimestamp()) . ')';
		$text .= elgg_format_element('div', ['class' => 'elgg-subtext'], $entity->getExcerpt(200));
		
		$returnvalue['text'] = $text;
		return $returnvalue;
	}
}
