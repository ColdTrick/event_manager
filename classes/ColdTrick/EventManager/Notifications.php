<?php

namespace ColdTrick\EventManager;

class Notifications {

	/**
	 * Prepare a notification message about a created event
	 *
	 * @param \Elgg\Hook $hook 'prepare', 'notification:create:object:event'
	 *
	 * @return Elgg_Notifications_Notification
	 */
	public static function prepareCreateEventNotification(\Elgg\Hook $hook) {
		$event = $hook->getParam('event');
		$entity = $event->getObject();
		$owner = $event->getActor();
		$language = $hook->getParam('language');
		
		$subject = elgg_echo('event_manager:notification:subject', [], $language);
		$summary = elgg_echo('event_manager:notification:summary', [], $language);
	
		$body = elgg_echo('event_manager:notification:body', [$owner->getDisplayName(), $entity->getDisplayName()], $language);
	
		if ($description = $entity->description) {
			$body .= PHP_EOL . PHP_EOL . elgg_get_excerpt($description);
		}
	
		$body .= PHP_EOL . PHP_EOL . $entity->getURL();
	
		$notification = $hook->getValue();
		$notification->subject = $subject;
		$notification->body = $body;
		$notification->summary = $summary;
	
		return $notification;
	}
}
