<?php

namespace ColdTrick\EventManager;

use Elgg\Email;
use Elgg\Email\Address;
use Elgg\Notifications\NotificationEvent;
use Elgg\Notifications\Notification;

class Notifications {

	/**
	 * Prepare a notification message about a created event
	 *
	 * @param \Elgg\Hook $hook 'prepare', 'notification:create:object:event'
	 *
	 * @return \Elgg\Notifications\NotificationEvent
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
	
	/**
	 * Make sure EventRegistration entities are never the sender of an e-mail
	 *
	 * To prevent e-mail exposure
	 *
	 * @param \Elgg\Hook $hook 'prepare', 'system:email'
	 *
	 * @return void|\Elgg\Email
	 */
	public static function prepareEventRegistrationSender(\Elgg\Hook $hook) {
		
		$email = $hook->getValue();
		if (!$email instanceof Email) {
			return;
		}
		
		if (!$email->getSender() instanceof \EventRegistration) {
			return;
		}
		
		$site = elgg_get_site_entity();
		
		$email->setSender($site);
		$email->setFrom(new Address($site->getEmailAddress(), $site->getDisplayName()));
		
		return $email;
	}
	
	/**
	 * Get the subscribers for the event mail to attendees
	 *
	 * @param \Elgg\Hook $hook 'get', 'subscriptions'
	 *
	 * @return void|array
	 */
	public static function getEventMailSubscriptions(\Elgg\Hook $hook) {
		
		$event = $hook->getParam('event');
		if (!$event instanceof NotificationEvent) {
			return;
		}
		
		$object = $event->getObject();
		if (!$object instanceof \EventMail) {
			return;
		}
		
		return $object->getMailSubscriptions();
	}
	
	/**
	 * Prepare the mail notification for the event mail
	 *
	 * @param \Elgg\Hook $hook 'prepare', 'notification:create:object:eventmail'
	 *
	 * @return void|Notification
	 */
	public static function prepareCreateEventMailNotification(\Elgg\Hook $hook) {
		
		$entity = $hook->getParam('object');
		if (!$entity instanceof \EventMail) {
			return;
		}
		
		$container = $entity->getContainerEntity();
		if (!$container instanceof \Event) {
			return;
		}
		
		$language = $hook->getParam('language');
		
		/* @var $result Notification */
		$result = $hook->getValue();
		
		$result->subject = elgg_echo('event_manager:mail:notification:subject', [
			$container->getDisplayName(),
			$entity->getDisplayName(),
		], $language);
		$result->body = elgg_echo('event_manager:mail:notification:body', [
			$entity->description,
			$container->getURL(),
		], $language);
		
		return $result;
	}
	
	/**
	 * Send the notification to the mail owner and cleanup the event mail object
	 *
	 * @param \Elgg\Hook $hook 'send:after', 'notifications'
	 *
	 * @return void
	 */
	public static function sendAfterEventMail(\Elgg\Hook $hook) {
		
		$event = $hook->getParam('event');
		if (!$event instanceof NotificationEvent) {
			return;
		}
		
		$entity = $event->getObject();
		if (!$entity instanceof \EventMail) {
			return;
		}
		
		$deliveries = $hook->getParam('deliveries');
		if (empty($deliveries[$entity->owner_guid]['email'])) {
			// mail was not send to owner
			$owner = $entity->getOwnerEntity();
			$container = $entity->getContainerEntity();
			
			$email = Email::factory([
				'to' => $owner,
				'subject' => elgg_echo('event_manager:mail:notification:subject', [
					$container->getDisplayName(),
					$entity->getDisplayName(),
				], $owner->getLanguage()),
				'body' => elgg_echo('event_manager:mail:notification:body', [
					$entity->description,
					$container->getURL(),
				], $owner->getLanguage()),
				'params' => [
					'object' => $entity,
					'action' => $event->getAction(),
				],
			]);
			
			elgg_send_email($email);
		}
		
		// remove the mail entity
		$entity->delete();
	}
}
