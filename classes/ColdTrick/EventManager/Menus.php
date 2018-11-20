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
		if (!$event instanceof \Event) {
			return;
		}
		
		if (!$event->canEdit()) {
			return;
		}

		// kick from event (assumes users listed on the view page of an event)
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'event_manager_kick',
			'text' => elgg_echo('event_manager:event:relationship:kick'),
			'href' => elgg_generate_action_url('event_manager/event/rsvp', [
				'guid' => $event->guid,
				'user' => $user->guid,
				'type' => EVENT_MANAGER_RELATION_UNDO,
			]),
			'section' => 'action',
		]);
	
		$user_relationship = $event->getRelationshipByUser($user->guid);
	
		if ($user_relationship == EVENT_MANAGER_RELATION_ATTENDING_PENDING) {
			
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'event_manager_resend_confirmation',
				'text' => elgg_echo("event_manager:event:menu:user_hover:resend_confirmation"),
				'href' => elgg_generate_action_url('event_manager/event/resend_confirmation', [
					'guid' => $event->guid,
					'user' => $user->guid,
				]),
				'section' => 'action',
			]);
		}
	
		if (in_array($user_relationship, [EVENT_MANAGER_RELATION_ATTENDING_PENDING, EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST])) {
			
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'event_manager_move_to_attendees',
				'text' => elgg_echo('event_manager:event:menu:user_hover:move_to_attendees'),
				'href' => elgg_generate_action_url('event_manager/attendees/move_to_attendees', [
					'guid' => $event->guid,
					'user' => $user->guid,
				]),
				'section' => 'action',
			]);
		}
		
		return $returnvalue;
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
		$entity = elgg_extract('entity', $params);
		if (!$entity instanceof \Event) {
			return;
		}
		
		// show an unregister link for non logged in users
		if (!elgg_is_logged_in() && $entity->register_nologin) {
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'unsubscribe',
				'text' => elgg_echo('event_manager:menu:unsubscribe'),
				'href' => elgg_generate_url('default:object:event:unsubscribe:request', [
					'guid' => $entity->guid,
				]),
				'priority' => 300,
			]);
		}
	
		return $returnvalue;
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
		if (!$group instanceof \ElggGroup) {
			return;
		}
	
		if (!event_manager_groups_enabled() || !$group->isToolEnabled('event_manager')) {
			return;
		}
	
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'events',
			'text' => elgg_echo('event_manager:menu:group_events'),
			'href' => elgg_generate_url('collection:object:event:group', ['guid' => $group->guid]),
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
		if (!$user instanceof \ElggUser) {
			return;
		}
	
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'events',
			'text' => elgg_echo('item:object:event'),
			'href' => elgg_generate_url('collection:object:event:owner', ['username' => $user->username]),
		]);
	
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
			$elggfile->setFilename($file->file);
			
			if (!$elggfile->exists()) {
				// check old storage location
				$elggfile->setFilename("files/{$file->file}");
			}
			
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => $file->title,
				'icon' => 'download',
				'text' => $file->title,
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
		if (elgg_get_plugin_setting('maps_provider', 'event_manager', 'google') !== 'none') {
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'events_map',
				'text' => elgg_echo('event_manager:list:navigation:onthemap'),
				'href' => 'javascript:void(0);',
				'rel' => 'onthemap',
			]);
		}
		if (elgg_is_logged_in()) {
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'attending',
				'text' => elgg_echo('event_manager:menu:attending'),
				'href' => elgg_generate_url('collection:object:event:attending', ['username' => elgg_get_logged_in_user_entity()->username]),
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
			'href' => elgg_generate_url('collection:object:event:owner', ['username' => elgg_get_logged_in_user_entity()->username]),
		]);
		
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'attending',
			'text' => elgg_echo('event_manager:menu:attending'),
			'href' => elgg_generate_url('collection:object:event:attending', ['username' => elgg_get_logged_in_user_entity()->username]),
			
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
	
	/**
	 * Register tabs for the event attendees page
	 *
	 * @param string          $hook        hook name
	 * @param string          $entity_type hook type
	 * @param \ElggMenuItem[] $returnvalue current return value
	 * @param array           $params      parameters
	 *
	 * @return void|\ElggMenuItem[]
	 */
	public static function registerEventAttendees($hook, $entity_type, $returnvalue, $params) {
		
		$entity = elgg_extract('entity', $params);
		if (!$entity instanceof \Event) {
			return;
		}
		
		$relationship = elgg_extract('relationship', $params);
		$valid_relationships = $entity->getSupportedRelationships();
		foreach ($valid_relationships as $rel => $label) {
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => $rel,
				'text' => $label,
				'href' => elgg_generate_url('collection:object:event:attendees', [
					'guid' => $entity->guid,
					'relationship' => $rel,
				]),
				'selected' => $relationship === $rel,
			]);
		}
		
		return $returnvalue;
	}
}
