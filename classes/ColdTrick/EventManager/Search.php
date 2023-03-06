<?php

namespace ColdTrick\EventManager;

/**
 * Search related callbacks
 */
class Search {
	
	/**
	 * Add searchable fields for events
	 *
	 * @param \Elgg\Event $event 'search:fields', 'object:event'
	 *
	 * @return array
	 */
	public static function addFields(\Elgg\Event $event) {
		
		$value = (array) $event->getValue();
		
		$defaults = [
			'metadata' => [],
		];
		
		$value = array_merge($defaults, $value);
		
		$fields = [
			'event_type',
			'location',
			'region',
			'shortdescription',
		];
		
		$value['metadata'] = array_merge($value['metadata'], $fields);
		
		return $value;
	}
	
	/**
	 * Add metadata names to be exported to Elasticsearch index
	 *
	 * @param \Elgg\Event $event 'export:metadata_names', 'elasticsearch'|'opensearch'
	 *
	 * @return void|array
	 */
	public static function exportMetadataNames(\Elgg\Event $event) {
		
		$entity = $event->getEntityParam();
		if (!$entity instanceof \Event) {
			return;
		}
		
		$result = $event->getValue();
		
		$result[] = 'event_type';
		$result[] = 'location';
		$result[] = 'region';
		$result[] = 'shortdescription';
		
		return $result;
	}
}
