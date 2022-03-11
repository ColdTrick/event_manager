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
		
	/**
	 * {@inheritDoc}
	 */
	protected static function isConfigurableForGroup(\ElggGroup $group): bool {
		return $group->isToolEnabled('event_manager');
	}
	
	/**
	 * Prevent enqueueing the notification if it should be sent in the future
	 *
	 * @param \Elgg\Hook $hook 'enqueue', 'notification'
	 *
	 * @return void|false
	 */
	public static function preventEnqueue(\Elgg\Hook $hook) {
		$action = $hook->getParam('action');
		$event = $hook->getParam('object');
		
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
	 * Track notification sent
	 *
	 * @param \Elgg\Hook $hook 'send:after', 'notifications'
	 *
	 * @return void|false
	 */
	public static function trackNotificationSent(\Elgg\Hook $hook) {
		$handler = $hook->getParam('handler');
		if (!$handler instanceof self) {
			return;
		}
		
		$event = $hook->getParam('event')->getObject();
		if ($event instanceof \Event) {
			$event->notification_sent_ts = time();
			unset($event->notification_queued_ts);
		}
	}
	
	/**
	 * Enqueue delayed notifications
	 *
	 * @param \Elgg\Hook $hook 'cron', 'daily'
	 *
	 * @return void|false
	 */
	public static function enqueueDelayedNotifications(\Elgg\Hook $hook) {
		elgg_call(ELGG_IGNORE_ACCESS, function() use ($hook) {
			$events = elgg_get_entities([
				'type' => 'object',
				'subtype' => 'event',
				'batch' => true,
				'batch_size' => 50,
				'limit' => false,
				'metadata_name_value_pairs' => [
					[
						'name' => 'notification_queued_ts',
						'value' => \Elgg\Values::normalizeTime(gmdate('c', $hook->getParam('time')))->setTime(0,0,0)->getTimestamp(), // keep inline with scheduling
						'operand' => '<=',
						'as' => ELGG_VALUE_INTEGER,
					],
				],
				'wheres' => [
					function (\Elgg\Database\QueryBuilder $qb, $main_alias) {
						return $qb->compare("{$main_alias}.access_id", '<>', ACCESS_PRIVATE, ELGG_VALUE_INTEGER);
					}
				]
			]);
			
			$session = elgg_get_session();
			$backup_user = $session->getLoggedInUser();
			foreach ($events as $event) {
				$session->setLoggedInUser($event->getOwnerEntity());
				_elgg_services()->notifications->enqueueEvent('create', 'object', $event);
			}
			if ($backup_user instanceof \ElggUser) {
				$session->setLoggedInUser($backup_user);
			} else {
				$session->removeLoggedInUser();
			}
		});
	}
}
