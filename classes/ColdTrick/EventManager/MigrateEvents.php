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
	public static function supportedSubtypes(\Elgg\Event $event) {
		
		$result = $event->getValue();
		
		$result[\Event::SUBTYPE] = static::class;
		
		return $result;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \ColdTrick\EntityTools\Migrate::canBackDate()
	 */
	public function canBackDate() {
		return true;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \ColdTrick\EntityTools\Migrate::canChangeContainer()
	 */
	public function canChangeContainer() {
		return true;
	}
	
	/**
	 * {@inheritdoc}
	 * @see \ColdTrick\EntityTools\Migrate::canChangeOwner()
	 */
	public function canChangeOwner() {
		return true;
	}
}
