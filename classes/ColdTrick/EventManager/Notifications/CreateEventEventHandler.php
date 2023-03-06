<?php

namespace ColdTrick\EventManager\Notifications;

use Elgg\Notifications\NotificationEventHandler;

/**
 * Notification Event Handler for 'object' 'event' 'create' action
 */
class CreateEventEventHandler extends NotificationEventHandler {

	/**
	 * {@inheritdoc}
	 */
	protected function getNotificationSubject(\ElggUser $recipient, string $method): string {
		return elgg_echo('event_manager:notification:subject', [$this->event->getObject()->getDisplayName()], $recipient->getLanguage());
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getNotificationSummary(\ElggUser $recipient, string $method): string {
		return elgg_echo('event_manager:notification:summary', [$this->event->getObject()->getDisplayName()], $recipient->getLanguage());
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function getNotificationBody(\ElggUser $recipient, string $method): string {
		$entity = $this->event->getObject();
		
		$body = elgg_echo('event_manager:notification:body', [
			$this->event->getActor()->getDisplayName(),
			$entity->getDisplayName(),
		], $recipient->getLanguage());
		
		$description = $entity->description;
		if (!empty($description)) {
			$body .= PHP_EOL . PHP_EOL . elgg_get_excerpt($description);
		}
	
		$body .= PHP_EOL . PHP_EOL . $entity->getURL();
		
		return $body;
	}
		
	/**
	 * {@inheritdoc}
	 */
	protected static function isConfigurableForGroup(\ElggGroup $group): bool {
		return $group->isToolEnabled('event_manager');
	}
	
	/**
	 * Prevent enqueueing the notification if it should be sent in the future
	 *
	 * @param \Elgg\Event $elgg_event 'enqueue', 'notification'
	 *
	 * @return void|false
	 */
	public static function preventEnqueue(\Elgg\Event $elgg_event) {
		$action = $elgg_event->getParam('action');
		$event = $elgg_event->getParam('object');
		
		if ($action !== 'create' || !$event instanceof \Event) {
			return;
		}
		
		if (!empty($event->notification_sent_ts)) {
			// for some reason a duplicate event was triggered
			return false;
		}
		
		if ((int) $event->notification_queued_ts > time()) {
			// notification should happen in the future
			return false;
		}
	}
	
	/**
	 * Track if a notification is actually queued so we can prevent extra notifications
	 *
	 * @param \Elgg\Event $elgg_event 'enqueue', 'notifications'
	 *
	 * @return void|false
	 */
	public static function trackNotificationSent(\Elgg\Event $elgg_event) {
		$event = $elgg_event->getObject();
		if (!$event instanceof \Event) {
			return;
		}
		
		if ($event->access_id === ACCESS_PRIVATE || !empty($event->notification_sent_ts)) {
			return;
		}
		
		$event->notification_sent_ts = time();
		unset($event->notification_queued_ts);
	}
	
	/**
	 * Enqueue delayed notifications
	 *
	 * @param \Elgg\Event $elgg_event 'cron', 'daily'
	 *
	 * @return void|false
	 */
	public static function enqueueDelayedNotifications(\Elgg\Event $elgg_event) {
		elgg_call(ELGG_IGNORE_ACCESS, function() use ($elgg_event) {
			$events = elgg_get_entities([
				'type' => 'object',
				'subtype' => 'event',
				'batch' => true,
				'batch_size' => 50,
				'limit' => false,
				'metadata_name_value_pairs' => [
					[
						'name' => 'notification_queued_ts',
						'value' => \Elgg\Values::normalizeTime(gmdate('c', $elgg_event->getParam('time')))->setTime(0, 0, 0)->modify('+1 days')->getTimestamp(), // keep inline with scheduling but add 1 day
						'operand' => '<',
						'as' => ELGG_VALUE_INTEGER,
					],
				],
				'wheres' => [
					function (\Elgg\Database\QueryBuilder $qb, $main_alias) {
						return $qb->compare("{$main_alias}.access_id", '<>', ACCESS_PRIVATE, ELGG_VALUE_INTEGER);
					}
				]
			]);
			
			$session_manager = _elgg_services()->session_manager;
			$backup_user = $session_manager->getLoggedInUser();
			foreach ($events as $event) {
				$session_manager->setLoggedInUser($event->getOwnerEntity());
				_elgg_services()->notifications->enqueueEvent('create', 'object', $event);
			}
			
			if ($backup_user instanceof \ElggUser) {
				$session_manager->setLoggedInUser($backup_user);
			} else {
				$session_manager->removeLoggedInUser();
			}
		});
	}
}
