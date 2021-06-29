<?php

namespace ColdTrick\EventManager\Notifications;

use Elgg\Notifications\NotificationEventHandler;

/**
 * Notification Event Handler for 'object' 'eventmail' 'create' action
 */
class CreateEventMailEventHandler extends NotificationEventHandler {

	/**
	 * {@inheritDoc}
	 */
	protected function getNotificationSubject(\ElggUser $recipient, string $method): string {
		$entity = $this->event->getObject();
		$container = $entity->getContainerEntity();
		
		return elgg_echo('event_manager:mail:notification:subject', [
			$container->getDisplayName(),
			$entity->getDisplayName(),
		], $recipient->getLanguage());
	}
	
	/**
	 * {@inheritDoc}
	 */
	protected function getNotificationBody(\ElggUser $recipient, string $method): string {
		$entity = $this->event->getObject();
		$container = $entity->getContainerEntity();
		
		return elgg_echo('event_manager:mail:notification:body', [
			$entity->description,
			$container->getURL(),
		], $recipient->getLanguage());
	}

	/**
	 * Return EventMail subscriptions
	 *
	 * {@inheritDoc}
	 */
	public function getSubscriptions(): array {
		return $this->event->getObject()->getMailSubscriptions();
	}
}
