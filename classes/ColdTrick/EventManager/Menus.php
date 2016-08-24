<?php

namespace ColdTrick\EventManager;

class Menus {
	/**
	 * Adds menu items to the user hover menu
	 *
	 * @param string $hook        hook name
	 * @param string $entity_type hook type
	 * @param array  $returnvalue current return value
	 * @param array  $params      parameters
	 *
	 * @return array
	 */
	public static function registerUserHover($hook, $entity_type, $returnvalue, $params) {
		$guid = get_input('guid');
		$user = elgg_extract('entity', $params);
		
		if (empty($guid) || empty($user)) {
			return;
		}
		$event = get_entity($guid);
		if (!($event instanceof \Event)) {
			return;
		}
		
		if (!$event->canEdit()) {
			return;
		}
		
		$result = $returnvalue;
	
		// kick from event (assumes users listed on the view page of an event)
		$href = 'action/event_manager/event/rsvp?guid=' . $event->getGUID() . '&user=' . $user->getGUID() . '&type=' . EVENT_MANAGER_RELATION_UNDO;
	
		$item = \ElggMenuItem::factory([
			'name' => 'event_manager_kick',
			'text' => elgg_echo('event_manager:event:relationship:kick'),
			'href' => $href,
			'is_action' => true,
			'section' => 'action',
		]);
		
		$result[] = $item;
	
		$user_relationship = $event->getRelationshipByUser($user->getGUID());
	
		if ($user_relationship == EVENT_MANAGER_RELATION_ATTENDING_PENDING) {
			// resend confirmation
			$href = 'action/event_manager/event/resend_confirmation?guid=' . $event->getGUID() . '&user=' . $user->getGUID();
	
			$item = \ElggMenuItem::factory([
				'name' => 'event_manager_resend_confirmation',
				'text' => elgg_echo("event_manager:event:menu:user_hover:resend_confirmation"),
				'href' => $href,
				'is_action' => true,
				'section' => 'action',
			]);
			
			$result[] = $item;
		}
	
		if (in_array($user_relationship, [EVENT_MANAGER_RELATION_ATTENDING_PENDING, EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST])) {
			// move to attendees
			$href = 'action/event_manager/attendees/move_to_attendees?guid=' . $event->getGUID() . '&user=' . $user->getGUID();
			
			$item = \ElggMenuItem::factory([
				'name' => 'event_manager_move_to_attendees',
				'text' => elgg_echo('event_manager:event:menu:user_hover:move_to_attendees'),
				'href' => $href,
				'is_action' => true,
				'section' => 'action',
			]);
	
			$result[] = $item;
		}
		
		return $result;
	}
	
	/**
	 * Adds menu items to the entity menu
	 *
	 * @param string $hook        hook name
	 * @param string $entity_type hook type
	 * @param array  $returnvalue current return value
	 * @param array  $params      parameters
	 *
	 * @return array
	 */
	public static function registerEntity($hook, $entity_type, $returnvalue, $params) {
		if (elgg_in_context('widgets')) {
			return;
		}
		
		$entity = elgg_extract('entity', $params);
		if (!($entity instanceof \Event)) {
			return;
		}
		
		$result = $returnvalue;
			
		$attendee_count = $entity->countAttendees();
		if ($attendee_count > 0 || $entity->openForRegistration()) {
			$result[] = \ElggMenuItem::factory([
				'name' => 'attendee_count',
				'priority' => 50,
				'text' => elgg_echo('event_manager:event:relationship:event_attending:entity_menu', [$attendee_count]),
				'href' => false,
			]);
		}
		
		// change some of the basic menus
		if (!empty($result) && is_array($result)) {
			foreach ($result as &$item) {
				switch ($item->getName()) {
					case 'edit':
						$item->setHref('events/event/edit/' . $entity->getGUID());
						break;
					case 'delete':
						$href = elgg_get_site_url() . 'action/event_manager/event/delete?guid=' . $entity->getGUID();
						$href = elgg_add_action_tokens_to_url($href);
	
						$item->setHref($href);
						$item->setConfirmText(elgg_echo('deleteconfirm'));
						break;
				}
			}
		}
	
		// show an unregister link for non logged in users
		if (!elgg_is_logged_in() && $entity->register_nologin) {
			$result[] = \ElggMenuItem::factory([
				'name' => 'unsubscribe',
				'text' => elgg_echo('event_manager:menu:unsubscribe'),
				'href' => 'events/unsubscribe/' . $entity->getGUID() . '/' . elgg_get_friendly_title($entity->title),
				'priority' => 300,
			]);
		}
	
		return $result;
	}
	
	/**
	 * add menu item for groups to owner block
	 *
	 * @param string $hook        hook name
	 * @param string $entity_type hook type
	 * @param array  $returnvalue current return value
	 * @param array  $params      parameters
	 *
	 * @return array
	 */
	public static function registerGroupOwnerBlock($hook, $entity_type, $returnvalue, $params) {
	
		$group = elgg_extract('entity', $params);
		if (!($group instanceof \ElggGroup)) {
			return;
		}
	
		if (!event_manager_groups_enabled() || $group->event_manager_enable == 'no') {
			return;
		}
	
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'events',
			'text' => elgg_echo('event_manager:menu:group_events'),
			'href' => 'events/event/list/' . $group->getGUID(),
		]);
	
		return $returnvalue;
	}
	
	/**
	 * add menu item to user owner block
	 *
	 * @param string $hook        hook name
	 * @param string $entity_type hook type
	 * @param array  $returnvalue current return value
	 * @param array  $params      parameters
	 *
	 * @return array
	 */
	public static function registerUserOwnerBlock($hook, $entity_type, $returnvalue, $params) {
	
		$user = elgg_extract('entity', $params);
		if (!($user instanceof \ElggUser)) {
			return;
		}
	
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'events',
			'text' => elgg_echo('item:object:event'),
			'href' => 'events/owner/' . $user->username,
		]);
	
		return $returnvalue;
	}
	
	/**
	 * Sidebar menu items on the event edit form
	 *
	 * @param string $hook        hook name
	 * @param string $entity_type hook type
	 * @param array  $returnvalue current return value
	 * @param array  $params      parameters
	 *
	 * @return array
	 */
	public static function registerEventEdit($hook, $entity_type, $returnvalue, $params) {
		$sections = ['profile', 'location', 'contact', 'registration', 'questions'];
		$entity = elgg_extract('entity', $params);
		
		foreach ($sections as $section) {
			$options = [
				'name' => "event_edit_{$section}",
				'text' => elgg_echo("event_manager:edit:form:tabs:{$section}"),
				'href' => "#event-manager-forms-event-edit-{$section}",
			];
			
			if ($section == 'questions') {
				if (!($entity instanceof \Event)) {
					$options['item_class'][] = 'hidden';
				} elseif (!$entity->getRegistrationFormQuestions(true) && !$entity->registration_needed) {
					$options['item_class'][] = 'hidden';
				}
			}
			
			$returnvalue[] = \ElggMenuItem::factory($options);
		}
		
		return $returnvalue;
	}
	
	/**
	 * Add menu items listing of event files
	 *
	 * @param string $hook        hook name
	 * @param string $entity_type hook type
	 * @param array  $returnvalue current return value
	 * @param array  $params      parameters
	 *
	 * @return array
	 */
	public static function registerEventFiles($hook, $entity_type, $returnvalue, $params) {
		$event = elgg_extract('entity', $params);
		if (!($event instanceof \Event)) {
			return;
		}
		
		$files = $event->hasFiles();
		if (empty($files)) {
			return;
		}
		
		$elggfile = new \ElggFile();
		$elggfile->owner_guid = $event->guid;
		
		$use_cookie = ($event->access_id !== ACCESS_PUBLIC);
		
		foreach ($files as $file) {
			$elggfile->setFilename("files/{$file->file}");
	
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => $file->title,
				'text' => elgg_view_icon('download', 'mrs') . $file->title,
				'href' => elgg_get_inline_url($elggfile, $use_cookie),
			]);
		}
		
		return $returnvalue;
	}
	
	/**
	 * Add filter tabs for event lists
	 *
	 * @param string $hook        hook name
	 * @param string $entity_type hook type
	 * @param array  $returnvalue current return value
	 * @param array  $params      parameters
	 *
	 * @return array
	 */
	public static function registerEventsList($hook, $entity_type, $returnvalue, $params) {
	
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'events_list',
			'text' => elgg_echo('event_manager:list:navigation:list'),
			'href' => 'javascript:void(0);',
			'rel' => 'list',
			'selected' => true,
		]);
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'events_calendar',
			'text' => elgg_echo('event_manager:list:navigation:calendar'),
			'href' => 'events/calendar',
			'rel' => 'calendar',
		]);
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'events_map',
			'text' => elgg_echo('event_manager:list:navigation:onthemap'),
			'href' => 'javascript:void(0);',
			'rel' => 'onthemap',
		]);
		if (elgg_is_logged_in()) {
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'attending',
				'text' => elgg_echo('event_manager:menu:attending'),
				'href' => 'events/attending/' . elgg_get_logged_in_user_entity()->username,
			]);
		}
		
		return $returnvalue;
	}
	
	/**
	 * Add filter tabs for user filter menu
	 *
	 * @param string $hook        hook name
	 * @param string $entity_type hook type
	 * @param array  $returnvalue current return value
	 * @param array  $params      parameters
	 *
	 * @return array
	 */
	public static function registerFilter($hook, $entity_type, $returnvalue, $params) {
		if (!elgg_in_context('events')) {
			return;
		}
		
		$returnvalue = [];
		
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'all',
			'text' => elgg_echo('all'),
			'href' => 'events',
		]);
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'mine',
			'text' => elgg_echo('mine'),
			'href' => 'events/owner/' . elgg_get_logged_in_user_entity()->username,
		]);
		
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'attending',
			'text' => elgg_echo('event_manager:menu:attending'),
			'href' => 'events/attending/' . elgg_get_logged_in_user_entity()->username,
		]);
		
		return $returnvalue;
	}
	
	/**
	 * Removes unwanted menu items from activity items if it is an event RSVP
	 *
	 * @param string $hook        hook name
	 * @param string $entity_type hook type
	 * @param array  $returnvalue current return value
	 * @param array  $params      parameters
	 *
	 * @return array
	 */
	public static function stripEventRelationshipRiverMenuItems($hook, $entity_type, $returnvalue, $params) {
		$item = elgg_extract('item', $params);
		if (!($item instanceof \ElggRiverItem)) {
			return;
		}
		if ($item->view !== 'river/event_relationship/create') {
			return;
		}
		
		foreach ($returnvalue as $key => $menu_item) {
			if ($menu_item->getName() === 'delete') {
				continue;
			}
			unset($returnvalue[$key]);
		}
		
		return $returnvalue;
	}
}