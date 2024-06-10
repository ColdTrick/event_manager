<?php

namespace ColdTrick\EventManager;

use ColdTrick\EntityTools\Migrate;

/**
 * Entity tools migrate
 */
class MigrateEvents extends Migrate {
	
	/**
	 * Add events to the supported types for EntityTools
	 *
	 * @param \Elgg\Event $event 'supported_types', 'entity_tools'
	 *
	 * @return array
	 */
	public static function supportedSubtypes(\Elgg\Event $event): array {
		
		$result = $event->getValue();
		
		$result[\Event::SUBTYPE] = static::class;
		
		return $result;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function canBackDate(): bool {
		return true;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function canChangeContainer(): bool {
		return true;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function canChangeOwner(): bool {
		return true;
	}
}
