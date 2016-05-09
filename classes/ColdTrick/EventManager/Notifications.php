<?php

namespace ColdTrick\EventManager;

class Notifications {

	/**
	 * Prepare a notification message about a created event
	 *
	 * @param string                          $hook         Hook name
	 * @param string                          $type         Hook type
	 * @param Elgg_Notifications_Notification $notification The notification to prepare
	 * @param array                           $params       Hook parameters
	 *
	 * @return Elgg_Notifications_Notification
	 */
	public static function prepareCreateEventNotification($hook, $type, $notification, $params) {
		$entity = $params['event']->getObject();
		$owner = $params['event']->getActor();
		$language = $params['language'];
		
		$subject = elgg_echo('event_manager:notification:subject', [], $language);
		$summary = elgg_echo('event_manager:notification:summary', [], $language);
	
		$body = elgg_echo('event_manager:notification:body', [$owner->name, $entity->title], $language);
	
		if ($description = $entity->description) {
			$body .= PHP_EOL . PHP_EOL . elgg_get_excerpt($description);
		}
	
		$body .= PHP_EOL . PHP_EOL . $entity->getURL();
	
		$notification->subject = $subject;
		$notification->body = $body;
		$notification->summary = $summary;
	
		return $notification;
	}
}