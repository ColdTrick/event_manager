<?php

namespace ColdTrick\EventManager;

/**
 * Menu related callbacks
 */
class Menus {
	
	/**
	 * add menu item for groups to owner block
	 *
	 * @param \Elgg\Event $event 'register', 'menu:owner_block'
	 *
	 * @return array
	 */
	public static function registerGroupOwnerBlock(\Elgg\Event $event) {
	
		$group = $event->getEntityParam();
		if (!$group instanceof \ElggGroup) {
			return;
		}
	
		if (!$group->canWriteToContainer(0, 'object', 'event') || !$group->isToolEnabled('event_manager')) {
			return;
		}
	
		$returnvalue = $event->getValue();
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
	 * @param \Elgg\Event $event 'register', 'menu:owner_block'
	 *
	 * @return array
	 */
	public static function registerUserOwnerBlock(\Elgg\Event $event) {
	
		$user = $event->getEntityParam();
		if (!$user instanceof \ElggUser) {
			return;
		}
	
		$returnvalue = $event->getValue();
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
	 * @param \Elgg\Event $elgg_event 'register', 'menu:event_files'
	 *
	 * @return array
	 */
	public static function registerEventFiles(\Elgg\Event $elgg_event) {
		$event = $elgg_event->getEntityParam();
		if (!$event instanceof \Event) {
			return;
		}
		
		$files = $event->getFiles();
		if (empty($files)) {
			return;
		}
		
		$elggfile = new \ElggFile();
		$elggfile->owner_guid = $event->guid;
		
		$use_cookie = ($event->access_id !== ACCESS_PUBLIC);
		$returnvalue = $elgg_event->getValue();
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
				'href' => elgg_get_download_url($elggfile, $use_cookie),
			]);
		}
		
		return $returnvalue;
	}
	
	/**
	 * Add filter tabs for event lists
	 *
	 * @param \Elgg\Event $event 'register', 'menu:filter:events'
	 *
	 * @return array
	 */
	public static function registerEventsList(\Elgg\Event $event) {
	
		$route_params = [
			'list_type' => get_input('list_type'),
			'tag' => get_input('tag'),
		];
		
		$page_owner = elgg_get_page_owner_entity();
		if ($page_owner instanceof \ElggGroup) {
			$route_params['guid'] = $page_owner->guid;
		}
		
		$selected = $event->getParam('filter_value');
		
		$returnvalue = $event->getValue();
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'live',
			'text' => elgg_echo('event_manager:list:navigation:live'),
			'href' => elgg_generate_url('collection:object:event:live', $route_params),
			'rel' => 'list',
			'selected' => $selected === 'live',
			'priority' => 100,
		]);
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'upcoming',
			'text' => elgg_echo('event_manager:list:navigation:upcoming'),
			'href' => elgg_generate_url('collection:object:event:upcoming', $route_params),
			'rel' => 'list',
			'selected' => $selected === 'upcoming',
			'priority' => 200,
		]);
		
		// user links (not in group context)
		if (!$page_owner instanceof \ElggGroup && elgg_is_logged_in()) {
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'attending',
				'text' => elgg_echo('event_manager:menu:attending'),
				'href' => elgg_generate_url('collection:object:event:attending', [
					'username' => elgg_get_logged_in_user_entity()->username,
					'list_type' => get_input('list_type'),
					'tag' => get_input('tag'),
				]),
				'selected' => $selected === 'attending',
				'priority' => 300,
			]);
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'mine',
				'text' => elgg_echo('mine'),
				'href' => elgg_generate_url('collection:object:event:owner', [
					'username' => elgg_get_logged_in_user_entity()->username,
					'list_type' => get_input('list_type'),
					'tag' => get_input('tag'),
				]),
				'selected' => $selected === 'mine',
				'priority' => 400,
			]);
		}
		
		return $returnvalue;
	}
	
	/**
	 * Register tabs for the event attendees page
	 *
	 * @param \Elgg\Event $event 'register', 'menu:event_attendees'
	 *
	 * @return void|\ElggMenuItem[]
	 */
	public static function registerEventAttendees(\Elgg\Event $event) {
		
		$entity = $event->getEntityParam();
		if (!$entity instanceof \Event) {
			return;
		}
		
		$relationship = $event->getParam('relationship');
		$valid_relationships = $entity->getSupportedRelationships();
		if (count($valid_relationships) === 1) {
			return;
		}
		
		$returnvalue = $event->getValue();
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
	 * @param \Elgg\Event $elgg_event 'register', 'menu:event:rsvp'
	 *
	 * @return \ElggMenuItem[]
	 */
	public static function registerRsvp(\Elgg\Event $elgg_event) {
		
		$event = $elgg_event->getEntityParam();
		if (!$event instanceof \Event) {
			return;
		}
		
		if (!$event->openForRegistration() && (!elgg_is_logged_in() || empty($event->getRelationshipByUser()))) {
			return;
		}
		
		$result = $elgg_event->getValue();
	
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
