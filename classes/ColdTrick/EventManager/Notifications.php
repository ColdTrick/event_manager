<?php

namespace ColdTrick\EventManager;

use Elgg\Email;
use Elgg\Email\Address;

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
}
