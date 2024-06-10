<?php

namespace ColdTrick\EventManager;

use Elgg\Email;
use Elgg\Email\Address;
use Elgg\Notifications\NotificationEvent;
use Elgg\Notifications\Notification;

/**
 * Notifications related callbacks
 */
class Notifications {

	/**
	 * Make sure EventRegistration entities are never the sender of an e-mail
	 *
	 * To prevent e-mail exposure
	 *
	 * @param \Elgg\Event $event 'prepare', 'system:email'
	 *
	 * @return void|\Elgg\Email
	 */
	public static function prepareEventRegistrationSender(\Elgg\Event $event) {
		
		$email = $event->getValue();
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
	 * Send the notification to the mail owner and cleanup the event mail object
	 *
	 * @param \Elgg\Event $elgg_event 'send:after', 'notifications'
	 *
	 * @return void
	 */
	public static function sendAfterEventMail(\Elgg\Event $elgg_event): void {
		
		$event = $elgg_event->getParam('event');
		if (!$event instanceof NotificationEvent) {
			return;
		}
		
		$entity = $event->getObject();
		if (!$entity instanceof \EventMail) {
			return;
		}
		
		// remove the mail entity
		$entity->delete();
	}
}
