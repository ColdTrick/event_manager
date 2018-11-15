<?php

namespace ColdTrick\EventManager\Event;

class Day extends \ElggObject {
	const SUBTYPE = 'eventday';

	/**
	 * initializes the default class attributes
	 *
	 * @return void
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();

		$this->attributes['subtype'] = self::SUBTYPE;
	}

	/**
	 * Returns the slots
	 *
	 * @return array|boolean
	 */
	public function getEventSlots() {
		return elgg_get_entities([
			'type' => 'object',
			'subtype' => Slot::SUBTYPE,
			'relationship_guid' => $this->guid,
			'relationship' => 'event_day_slot_relation',
			'inverse_relationship' => true,
			'order_by_metadata' => [
				'name' => 'start_time',
				'as' => 'integer'
			],
			'limit' => false,
		]);
	}
}
