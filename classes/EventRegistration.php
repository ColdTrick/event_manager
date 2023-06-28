<?php

/**
 * EventRegistration
 *
 * @property string $name  the name of the user
 * @property string $email the e-mail address of the user
 */
class EventRegistration extends \ElggObject {
	
	const SUBTYPE = 'eventregistration';

	/**
	 * {@inheritdoc}
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();

		$this->attributes['subtype'] = self::SUBTYPE;
		$this->attributes['access_id'] = ACCESS_PUBLIC;
	}
	
	/**
	 * Allow non-user to remove their registration correctly
	 *
	 * {@inheritdoc}
	 */
	public function canEdit(int $user_guid = 0): bool {
		global $EVENT_MANAGER_UNDO_REGISTRATION;
	
		if (!empty($EVENT_MANAGER_UNDO_REGISTRATION)) {
			return true;
		}

		return parent::canEdit($user_guid);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getDisplayName(): string {
		return $this->name ?? parent::getDisplayName();
	}
}
