<?php

namespace ColdTrick\EventManager;

/**
 * Access related callbacks
 */
class Access {
	
	/**
	 * After the event is updated in the database make sure the owned entities have the same access_id
	 *
	 * @param \Elgg\Event $event 'update:after', 'object'
	 *
	 * @return void
	 */
	public static function updateEvent(\Elgg\Event $event) {
		$entity = $event->getObject();
		if (!$entity instanceof \Event) {
			return;
		}
		
		$org_attributes = $entity->getOriginalAttributes();
		if (elgg_extract('access_id', $org_attributes) === null) {
			// access wasn't updated
			return;
		}
		
		elgg_call(ELGG_IGNORE_ACCESS, function() use ($entity) {
			$days = $entity->getEventDays();
			if (!empty($days)) {
				foreach ($days as $day) {
					$day->access_id = $entity->access_id;
					$day->save();
				
					$slots = $day->getEventSlots();
					if (empty($slots)) {
						continue;
					}
				
					foreach ($slots as $slot) {
						$slot->access_id = $entity->access_id;
						$slot->save();
					}
				}
			}
			
			$questions = $entity->getRegistrationFormQuestions();
			if (!empty($questions)) {
				foreach ($questions as $question) {
					$question->access_id = $entity->access_id;
					$question->save();
				}
			}
		});
	}
	
	/**
	 * Checks if plugin setting allows users to write to a container
	 *
	 * @param \Elgg\Event $elgg_event 'container_logic_check', 'object'
	 *
	 * @return void|false
	 */
	public static function containerLogicCheck(\Elgg\Event $elgg_event) {
		if ($elgg_event->getParam('subtype') !== 'event') {
			return;
		}
		
		$container = $elgg_event->getParam('container');
		if ($container instanceof \ElggGroup) {
			$who_create_group_events = elgg_get_plugin_setting('who_create_group_events', 'event_manager'); // group_admin, members
			if (empty($who_create_group_events)) {
				// no one can create
				return false;
			}
			
			$user = $elgg_event->getUserParam();
			$user_guid = $user instanceof \ElggUser ? $user->guid : 0;
			if ($who_create_group_events === 'group_admin' && !$container->canEdit($user_guid)) {
				return false;
			}
			
			// in other group case let regular checks take place
		} else {
			$who_create_site_events = elgg_get_plugin_setting('who_create_site_events', 'event_manager');
			if ($who_create_site_events === 'admin_only' && !elgg_is_admin_logged_in()) {
				return false;
			}
		}
	}
}
