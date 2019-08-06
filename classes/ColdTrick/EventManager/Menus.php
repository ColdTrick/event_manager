<?php

namespace ColdTrick\EventManager;

class Menus {
	/**
	 * Adds menu items to the attendees entity menu
	 *
	 * @param \Elgg\Hook $hook 'register', 'menu:entity'
	 *
	 * @return array
	 */
	public static function registerAttendeeActions(\Elgg\Hook $hook) {
		
		$entity = $hook->getEntityParam();
		if (!$entity instanceof \ElggUser && !$entity instanceof \EventRegistration) {
			return;
		}
		
		$route = _elgg_services()->request->getRoute();
		if (!$route || $route->getName() !== 'collection:object:event:attendees') {
			return;
		}
		
		$event = get_entity((int) elgg_extract('guid', $route->getMatchedParameters()));
		if (!$event instanceof \Event) {
			return;
		}
		
		if (!$event->canEdit()) {
			return;
		}
		
		$returnvalue = $hook->getValue();
		
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
				'text' => elgg_echo("event_manager:event:menu:user_hover:resend_confirmation"),
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
	 * Adds menu items to the entity menu
	 *
	 * @param \Elgg\Hook $hook 'register', 'menu:entity'
	 *
	 * @return array
	 */
	public static function registerEntity(\Elgg\Hook $hook) {
		$entity = $hook->getEntityParam();
		if (!$entity instanceof \Event) {
			return;
		}
		
		if (elgg_is_logged_in() || !$entity->register_nologin) {
			return;
		}
		
		// show an unregister link for non logged in users
		$returnvalue = $hook->getValue();
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
	
	/**
	 * add menu item for groups to owner block
	 *
	 * @param \Elgg\Hook $hook 'register', 'menu:owner_block'
	 *
	 * @return array
	 */
	public static function registerGroupOwnerBlock(\Elgg\Hook $hook) {
	
		$group = $hook->getEntityParam();
		if (!$group instanceof \ElggGroup) {
			return;
		}
	
		if (!$group->canWriteToContainer(0, 'object', 'event') || !$group->isToolEnabled('event_manager')) {
			return;
		}
	
		$returnvalue = $hook->getValue();
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
	 * @param \Elgg\Hook $hook 'register', 'menu:owner_block'
	 *
	 * @return array
	 */
	public static function registerUserOwnerBlock(\Elgg\Hook $hook) {
	
		$user = $hook->getEntityParam();
		if (!$user instanceof \ElggUser) {
			return;
		}
	
		$returnvalue = $hook->getValue();
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
	 * @param \Elgg\Hook $hook 'register', 'menu:event_files'
	 *
	 * @return array
	 */
	public static function registerEventFiles(\Elgg\Hook $hook) {
		$event = $hook->getEntityParam();
		if (!$event instanceof \Event) {
			return;
		}
		
		$files = $event->hasFiles();
		if (empty($files)) {
			return;
		}
		
		$elggfile = new \ElggFile();
		$elggfile->owner_guid = $event->guid;
		
		$use_cookie = ($event->access_id !== ACCESS_PUBLIC);
		$returnvalue = $hook->getValue();
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
	 * @param \Elgg\Hook $hook 'register', 'menu:filter:events'
	 *
	 * @return array
	 */
	public static function registerEventsList(\Elgg\Hook $hook) {
	
		$route_params = [];
		$page_owner = elgg_get_page_owner_entity();
		if ($page_owner instanceof \ElggGroup) {
			$route_params['guid'] = $page_owner->guid;
		}
		
		$selected = $hook->getParam('filter_value');
		
		$returnvalue = $hook->getValue();
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'live',
			'text' => elgg_echo('event_manager:list:navigation:live'),
			'href' => elgg_generate_url('collection:object:event:live', $route_params),
			'rel' => 'list',
			'selected' => $selected === 'live',
		]);
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'upcoming',
			'text' => elgg_echo('event_manager:list:navigation:upcoming'),
			'href' => elgg_generate_url('collection:object:event:upcoming', $route_params),
			'rel' => 'list',
			'selected' => $selected === 'upcoming',
		]);
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'calendar',
			'text' => elgg_echo('event_manager:list:navigation:calendar'),
			'href' => elgg_generate_url('collection:object:event:calendar', $route_params),
			'rel' => 'calendar',
			'selected' => $selected === 'calendar',
		]);
		if (elgg_get_plugin_setting('maps_provider', 'event_manager') !== 'none') {
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'map',
				'text' => elgg_echo('event_manager:list:navigation:onthemap'),
				'href' => elgg_generate_url('collection:object:event:map', $route_params),
				'rel' => 'onthemap',
				'selected' => $selected === 'map',
			]);
		}
		
		// user links (not in group context)
		if (!$page_owner instanceof \ElggGroup && elgg_is_logged_in()) {
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'attending',
				'text' => elgg_echo('event_manager:menu:attending'),
				'href' => elgg_generate_url('collection:object:event:attending', [
					'username' => elgg_get_logged_in_user_entity()->username,
				]),
				'selected' => $selected === 'attending',
			]);
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'mine',
				'text' => elgg_echo('mine'),
				'href' => elgg_generate_url('collection:object:event:owner', [
					'username' => elgg_get_logged_in_user_entity()->username,
				]),
				'selected' => $selected === 'mine',
			]);
		}
		
		return $returnvalue;
	}
	
	/**
	 * Removes unwanted menu items from activity items if it is an event RSVP
	 *
	 * @param \Elgg\Hook $hook 'register', 'menu:river'
	 *
	 * @return array
	 */
	public static function stripEventRelationshipRiverMenuItems(\Elgg\Hook $hook) {
		$item = $hook->getParam('item');
		if (!$item instanceof \ElggRiverItem) {
			return;
		}
		if ($item->view !== 'river/event_relationship/create') {
			return;
		}
		
		$returnvalue = $hook->getValue();
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
	 * @param \Elgg\Hook $hook 'register', 'menu:event_attendees'
	 *
	 * @return void|\ElggMenuItem[]
	 */
	public static function registerEventAttendees(\Elgg\Hook $hook) {
		
		$entity = $hook->getEntityParam();
		if (!$entity instanceof \Event) {
			return;
		}
		
		$relationship = $hook->getParam('relationship');
		$valid_relationships = $entity->getSupportedRelationships();
		if (count($valid_relationships) === 1) {
			return;
		}
		
		$returnvalue = $hook->getValue();
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
	
	/**
	 * Registers menu items for the rsvp menu
	 *
	 * @param \Elgg\Hook $hook 'register', 'menu:event:rsvp'
	 *
	 * @return \ElggMenuItem[]
	 */
	public static function registerRsvp(\Elgg\Hook $hook) {
		
		$event = $hook->getEntityParam();
		if (!$event instanceof \Event) {
			return;
		}
		
		if (!$event->openForRegistration()) {
			return;
		}
		
		$result = $hook->getValue();
	
		if (elgg_is_logged_in()) {
			$event_relationship_options = event_manager_event_get_relationship_options();
			
			$user_relation = $event->getRelationshipByUser();
			if ($user_relation) {
				if (!in_array($user_relation, $event_relationship_options)) {
					$event_relationship_options[] = $user_relation;
				}
			}
			
			if (in_array($user_relation, $event_relationship_options)) {
				$event_relationship_options = [$user_relation];
			}
			
			foreach ($event_relationship_options as $rel) {
				if (($rel == EVENT_MANAGER_RELATION_ATTENDING) || ($rel == EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST) || $event->$rel) {
					
					if ($rel == EVENT_MANAGER_RELATION_ATTENDING && ($user_relation !== EVENT_MANAGER_RELATION_ATTENDING)) {
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
							if ($rel === EVENT_MANAGER_RELATION_ATTENDING) {
								$link_class[] = 'elgg-button-submit';
							} else {
								$link_class[] = 'elgg-button-action';
							}
							
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
			} elseif ($hook->getParam('full_view')) {
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
