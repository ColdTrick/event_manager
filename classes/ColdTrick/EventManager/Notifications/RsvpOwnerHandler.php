<?php

namespace ColdTrick\EventManager\Notifications;

use Elgg\Notifications\InstantNotificationEventHandler;

/**
 * Send a rsvp notification to an event owner/organizer/contactperson
 */
class RsvpOwnerHandler extends InstantNotificationEventHandler {
	
	/**
	 * {@inheritdoc}
	 */
	protected function getNotificationSubject(\ElggUser $recipient, string $method): string {
		if ($this->getParam('rsvp_type') === EVENT_MANAGER_RELATION_UNDO) {
			return elgg_echo('event_manager:event:registration:notification:owner:subject:event_undo', [$this->getEventEntity()?->getDisplayName()]);
		}

		return elgg_echo('event_manager:event:registration:notification:owner:subject', [$this->getEventEntity()?->getDisplayName()]);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getNotificationSummary(\ElggUser $recipient, string $method): string {
		return elgg_echo('event_manager:event:registration:notification:owner:summary:' . $this->getParam('rsvp_type'), [
			$this->getEventActor()?->getDisplayName(),
			$this->getEventEntity()?->getDisplayName(),
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getNotificationBody(\ElggUser $recipient, string $method): string {
		return elgg_echo('event_manager:event:registration:notification:owner:text:' . $this->getParam('rsvp_type'), [
			$this->getEventEntity()?->getDisplayName(),
			(string) $this->getParam('event_title_link'),
		]) . $this->getParam('registration_link');
	}
}
