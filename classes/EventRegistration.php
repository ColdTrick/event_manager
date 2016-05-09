<?php
/**
 * EventRegistration
 *
 * @package EventManager
 *
 */
class EventRegistration extends \ElggObject {
	const SUBTYPE = 'eventregistration';

	/**
	 * initializes the default class attributes
	 *
	 * @return void
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();

		$this->attributes['subtype'] = self::SUBTYPE;
		$this->attributes['access_id'] = ACCESS_PUBLIC;
	}
	
	/**
	 * Allow non user to remove their registration correctly
	 *
	 * {@inheritdoc}
	 */
	public function canEdit($user_guid = 0) {
		global $EVENT_MANAGER_UNDO_REGISTRATION;
	
		if (!empty($EVENT_MANAGER_UNDO_REGISTRATION)) {
			return true;
		}

		return parent::canEdit($user_guid);
	}
}
