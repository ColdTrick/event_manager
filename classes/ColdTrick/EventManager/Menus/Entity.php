<?php

namespace ColdTrick\EventManager\Menus;

use Elgg\Menu\MenuItems;

/**
 * Entity menu related callbacks
 */
class Entity {
	
	/**
	 * Register mail attendees to the entity menu
	 *
	 * @param \Elgg\Event $event 'register', 'menu:entity'
	 *
	 * @return void|MenuItems
	 */
	public static function registerMailAttendees(\Elgg\Event $event) {
		
		$entity = $event->getEntityParam();
		if (!$entity instanceof \Event || !$entity->canEdit()) {
			return;
		}
		
		if (!(bool) elgg_get_plugin_setting('event_mail', 'event_manager')) {
			return;
		}
		
		$result = $event->getValue();
		
		$result[] = \ElggMenuItem::factory([
			'name' => 'event_mail',
			'icon' => 'envelope',
			'text' => elgg_echo('event_manager:menu:mail'),
			'href' => elgg_generate_entity_url($entity, 'mail'),
		]);
		
		return $result;
	}
	
	/**
	 * Adds menu items to the attendees entity menu
	 *
	 * @param \Elgg\Event $elgg_event 'register', 'menu:entity'
	 *
	 * @return array
	 */
	public static function registerAttendeeActions(\Elgg\Event $elgg_event) {
		
		$entity = $elgg_event->getEntityParam();
		if (!$entity instanceof \ElggUser && !$entity instanceof \EventRegistration) {
			return;
		}
		
		$route = _elgg_services()->request->getRoute();
		if (!$route || ($route->getName() !== 'collection:object:event:attendees') && (elgg_extract('segments', $route->getMatchedParameters()) !== 'view/event_manager/event/attendees_list')) {
			return;
		}
		
		$event = get_entity((int) elgg_extract('guid', $route->getMatchedParameters(), get_input('guid')));
		if (!$event instanceof \Event) {
			return;
		}
		
		if (!$event->canEdit()) {
			return;
		}
		
		$returnvalue = $elgg_event->getValue();
		
		// no delete menu item
		$returnvalue->remove('delete');
		
		// kick from event (assumes users listed on the view page of an event)
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'event_manager_kick',
			'icon' => 'user-times',
			'text' => elgg_echo('event_manager:event:relationship:kick'),
			'href' => elgg_generate_action_url('event_manager/event/rsvp', [
				'guid' => $event->guid,
				'user' => $entity->guid,
				'type' => EVENT_MANAGER_RELATION_UNDO,
			]),
			'section' => 'action',
		]);
		
		$user_relationship = $event->getRelationshipByUser($entity->guid);
		
		if ($user_relationship == EVENT_MANAGER_RELATION_ATTENDING_PENDING) {
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'event_manager_resend_confirmation',
				'icon' => 'user-times',
				'text' => elgg_echo('event_manager:event:menu:user_hover:resend_confirmation'),
				'href' => elgg_generate_action_url('event_manager/event/resend_confirmation', [
					'guid' => $event->guid,
					'user' => $entity->guid,
				]),
				'section' => 'action',
			]);
		}
		
		if (in_array($user_relationship, [EVENT_MANAGER_RELATION_ATTENDING_PENDING, EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST])) {
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'event_manager_move_to_attendees',
				'icon' => 'user-times',
				'text' => elgg_echo('event_manager:event:menu:user_hover:move_to_attendees'),
				'href' => elgg_generate_action_url('event_manager/attendees/move_to_attendees', [
					'guid' => $event->guid,
					'user' => $entity->guid,
				]),
				'section' => 'action',
			]);
		}
		
		return $returnvalue;
	}
	
	/**
	 * Adds menu items to the event entity menu for non logged in users
	 *
	 * @param \Elgg\Event $event 'register', 'menu:entity'
	 *
	 * @return array
	 */
	public static function registerEventUnsubscribe(\Elgg\Event $event) {
		$entity = $event->getEntityParam();
		if (!$entity instanceof \Event) {
			return;
		}
		
		if (elgg_is_logged_in() || !$entity->register_nologin) {
			return;
		}
		
		// show an unregister link for non logged in users
		$returnvalue = $event->getValue();
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'unsubscribe',
			'icon' => 'sign-out-alt',
			'text' => elgg_echo('event_manager:menu:unsubscribe'),
			'href' => elgg_generate_url('default:object:event:unsubscribe:request', [
				'guid' => $entity->guid,
			]),
			'priority' => 300,
		]);
		return $returnvalue;
	}
}
