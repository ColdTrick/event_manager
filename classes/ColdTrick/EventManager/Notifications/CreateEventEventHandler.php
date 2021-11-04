<?php

namespace ColdTrick\EventManager\Notifications;

use Elgg\Notifications\NotificationEventHandler;

/**
 * Notification Event Handler for 'object' 'event' 'create' action
 */
class CreateEventEventHandler extends NotificationEventHandler {

	/**
	 * {@inheritDoc}
	 */
	protected function getNotificationSubject(\ElggUser $recipient, string $method): string {
		return elgg_echo('event_manager:notification:subject', [$this->event->getObject()->getDisplayName()], $recipient->getLanguage());
	}

	/**
	 * {@inheritDoc}
	 */
	protected function getNotificationSummary(\ElggUser $recipient, string $method): string {
		return elgg_echo('event_manager:notification:summary', [$this->event->getObject()->getDisplayName()], $recipient->getLanguage());
	}
	
	/**
	 * {@inheritDoc}
	 */
	protected function getNotificationBody(\ElggUser $recipient, string $method): string {
		$entity = $this->event->getObject();
		
		$body = elgg_echo('event_manager:notification:body', [
			$this->event->getActor()->getDisplayName(),
			$entity->getDisplayName(),
		], $recipient->getLanguage());
		
		if ($description = $entity->description) {
			$body .= PHP_EOL . PHP_EOL . elgg_get_excerpt($description);
		}
	
		$body .= PHP_EOL . PHP_EOL . $entity->getURL();
		
		return $body;
	}
}
