<?php

namespace ColdTrick\EventManager\Event;

/**
 * Event day
 */
class Day extends \ElggObject {
	
	const SUBTYPE = 'eventday';

	/**
	 * {@inheritdoc}
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();

		$this->attributes['subtype'] = self::SUBTYPE;
	}

	/**
	 * Returns the slots
	 *
	 * @return array
	 */
	public function getEventSlots(): array {
		return elgg_get_entities([
			'type' => 'object',
			'subtype' => Slot::SUBTYPE,
			'relationship_guid' => $this->guid,
			'relationship' => 'event_day_slot_relation',
			'inverse_relationship' => true,
			'sort_by' => [
				'property' => 'start_time',
				'signed' => true,
			],
			'limit' => false,
		]);
	}
}
