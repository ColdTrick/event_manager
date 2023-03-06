<?php

namespace ColdTrick\EventManager\Notifications;

use Elgg\Notifications\NotificationEventHandler;

/**
 * Notification Event Handler for 'object' 'eventmail' 'create' action
 */
class CreateEventMailEventHandler extends NotificationEventHandler {

	/**
	 * {@inheritdoc}
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
	 * {@inheritdoc}
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
	 * {@inheritdoc}
	 */
	public function getSubscriptions(): array {
		return $this->event->getObject()->getMailSubscriptions();
	}
	
	/**
	 * {@inheritdoc}
	 */
	public static function isConfigurableByUser(): bool {
		return false;
	}
}
