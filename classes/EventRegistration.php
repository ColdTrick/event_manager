<?php
/**
 * EventRegistration
 *
 * @package EventManager
 *
 */
class EventRegistration extends ElggObject {
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
}
