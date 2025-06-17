<?php

namespace ColdTrick\EventManager\Menus;

use Elgg\Menu\MenuItems;

/**
 * Event Rsvp menu related callbacks
 */
class EventRsvp {
	
	/**
	 * Registers menu items for the rsvp menu
	 *
	 * @param \Elgg\Event $elgg_event 'register', 'menu:event:rsvp'
	 *
	 * @return null|MenuItems
	 */
	public static function register(\Elgg\Event $elgg_event): ?MenuItems {
		
		$event = $elgg_event->getEntityParam();
		if (!$event instanceof \Event) {
			return null;
		}
		
		if (!$event->openForRegistration() && (!elgg_is_logged_in() || empty($event->getRelationshipByUser()))) {
			return null;
		}
		
		$result = $elgg_event->getValue();
		
		if (elgg_is_logged_in()) {
			$event_relationship_options = event_manager_event_get_relationship_options();
			
			$user_relation = $event->getRelationshipByUser();
			if ($user_relation && !in_array($user_relation, $event_relationship_options)) {
				$event_relationship_options[] = $user_relation;
			}
			
			if (in_array($user_relation, $event_relationship_options)) {
				$event_relationship_options = [$user_relation];
			}
			
			foreach ($event_relationship_options as $rel) {
				if (($rel === EVENT_MANAGER_RELATION_ATTENDING) || ($rel === EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST) || $event->$rel) {
					if ($rel === EVENT_MANAGER_RELATION_ATTENDING && ($user_relation !== EVENT_MANAGER_RELATION_ATTENDING)) {
						if (!$event->hasEventSpotsLeft() && !$event->waiting_list_enabled) {
							continue;
						}
					}
					
					if ($rel == $user_relation) {
						$result[] = \ElggMenuItem::factory([
							'name' => 'undo',
							'href' => elgg_generate_action_url('event_manager/event/rsvp', [
								'guid' => $event->guid,
								'type' => EVENT_MANAGER_RELATION_UNDO,
							]),
							'confirm' => true,
							'link_class' => ['elgg-button', 'elgg-button-cancel'],
							'text' => elgg_echo("event_manager:event:relationship:{$rel}:undo"),
							'icon' => 'undo',
						]);
					} else {
						if ($rel != EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST) {
							$link_class = ['elgg-button'];
							$link_class[] = ($rel === EVENT_MANAGER_RELATION_ATTENDING) ? 'elgg-button-submit' : 'elgg-button-action';
							
							$result[] = \ElggMenuItem::factory([
								'name' => $rel,
								'href' => elgg_generate_action_url('event_manager/event/rsvp', [
									'guid' => $event->guid,
									'type' => $rel,
								]),
								'text' => elgg_echo("event_manager:event:relationship:{$rel}"),
								'link_class' => $link_class,
							]);
						}
					}
				}
			}
		} else {
			if ($event->register_nologin) {
				$result[] = \ElggMenuItem::factory([
					'name' => 'register',
					'href' => elgg_generate_url('default:object:event:register', [
						'guid' => $event->guid,
					]),
					'text' => elgg_echo('event_manager:event:register:register_link'),
					'link_class' => ['elgg-button', 'elgg-button-submit'],
				]);
			} elseif ($elgg_event->getParam('full_view')) {
				$result[] = \ElggMenuItem::factory([
					'name' => 'log_in_first',
					'href' => 'login',
					'text' => elgg_echo('event_manager:event:register:log_in_first'),
					'link_class' => ['elgg-button', 'elgg-button-action'],
				]);
			}
		}
		
		return $result;
	}
}
