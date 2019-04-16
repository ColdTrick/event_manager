<?php

namespace ColdTrick\EventManager;

class Elasticsearch {
	
	/**
	 * Add metadata names to be exported to Elasticsearch index
	 *
	 * @param \Elgg\Hook $hook 'export:metadata_names', 'elasticsearch'
	 *
	 * @return void|array
	 */
	public static function exportMetadataNames(\Elgg\Hook $hook) {
		
		$entity = $hook->getEntityParam();
		if (!$entity instanceof \Event) {
			return;
		}
		
		$result = $hook->getValue();
		
		$result[] = 'event_type';
		$result[] = 'location';
		$result[] = 'region';
		$result[] = 'shortdescription';
		
		return $result;
	}
}
