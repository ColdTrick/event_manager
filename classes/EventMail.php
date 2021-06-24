<?php

/**
 * @property string[] $recipients who are the recipients of the mail
 */
class EventMail extends \ElggObject {
	
	/**
	 * @var string
	 */
	const SUBTYPE = 'eventmail';
	
	/**
	 * {@inheritDoc}
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();
		
		$this->attributes['subtype'] = self::SUBTYPE;
		$this->attributes['access_id'] = ACCESS_LOGGED_IN;
	}
	
	/**
	 * Get the subscriptions for the mail notification, based on the form settings
	 *
	 * @return array
	 */
	public function getMailSubscriptions(): array {
		$result = [
			$this->owner_guid => ['email'],
		];
		
		/* @var $event \Event */
		$event = $this->getContainerEntity();
		$row_to_guid = function($row) {
			return (int) $row->guid;
		};
		
		$recipients = $this->recipients;
		if (empty($recipients)) {
			// how is this possible?
			return $result;
		}
		
		if (!is_array($recipients)) {
			$recipients = [$recipients];
		}
		
		foreach ($recipients as $recipient_type) {
			$guids = [];
			
			switch ($recipient_type) {
				case 'contacts':
					$guids = $event->getContacts([
						'callback' => $row_to_guid,
					]);
					break;
				case EVENT_MANAGER_RELATION_ORGANIZING:
					// no longer a relationship
					$guids = $event->getOrganizers([
						'callback' => $row_to_guid,
					]);
					break;
				default:
					// default relationships
					$guids = $event->getEntitiesFromRelationship([
						'relationship' => $recipient_type,
						'limit' => false,
						'callback' => $row_to_guid,
					]);
					break;
			}
			
			foreach ($guids as $guid) {
				$result[$guid] = ['email'];
			}
		}
		
		return $result;
	}
}
