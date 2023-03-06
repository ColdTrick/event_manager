<?php

namespace ColdTrick\EventManager\Event;

/**
 * Event slot
 */
class Slot extends \ElggObject {
	
	const SUBTYPE = 'eventslot';

	/**
	 * {@inheritdoc}
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();

		$this->attributes['subtype'] = self::SUBTYPE;
	}

	/**
	 * Counts the number of registrations
	 *
	 * @return int
	 */
	public function countRegistrations(): int {
		return elgg_call(ELGG_IGNORE_ACCESS, function() {
			return elgg_count_entities([
				'relationship' => EVENT_MANAGER_RELATION_SLOT_REGISTRATION,
				'relationship_guid' => $this->guid,
				'inverse_relationship' => true,
			]);
		});
	}

	/**
	 * Returns if the slot has a spot left
	 *
	 * @return boolean
	 */
	public function hasSpotsLeft(): bool {
		if (empty($this->max_attendees) || (($this->max_attendees - $this->countRegistrations()) > 0)) {
			return true;
		}

		return false;
	}

	/**
	 * Returns the users waiting for this slot
	 *
	 * @param boolean $count if a count should be returned, or the entity
	 *
	 * @return boolean|int|array
	 */
	public function getWaitingUsers(bool $count = false) {
		return elgg_call(ELGG_IGNORE_ACCESS, function() use ($count) {
			if ($count) {
				return $this->countEntitiesFromRelationship(EVENT_MANAGER_RELATION_SLOT_REGISTRATION_WAITINGLIST, true);
			}
			
			return $this->getEntitiesFromRelationship([
				'relationship' => EVENT_MANAGER_RELATION_SLOT_REGISTRATION_WAITINGLIST,
				'inverse_relationship' => true,
			]);
		});
	}
}
