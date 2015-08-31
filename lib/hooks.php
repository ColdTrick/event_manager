<?php
/**
 * Hook are bundled here
 */

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
function event_manager_user_hover_menu($hook, $entity_type, $returnvalue, $params) {
	$guid = get_input('guid');
	$user = elgg_extract('entity', $params);
	
	if (empty($guid) || empty($user)) {
		return;
	}
	$event = get_entity($guid);
	if (empty($event) || ($event->getSubtype() !== Event::SUBTYPE)) {
		return;
	}
	
	if (!$event->canEdit()) {
		return;
	}
	
	$result = $returnvalue;

	// kick from event (assumes users listed on the view page of an event)
	$href = 'action/event_manager/event/rsvp?guid=' . $event->getGUID() . '&user=' . $user->getGUID() . '&type=' . EVENT_MANAGER_RELATION_UNDO;

	$item = ElggMenuItem::factory([
		'name' => 'event_manager_kick', 
		'text' => elgg_echo("event_manager:event:relationship:kick"), 
		'href' => $href,
		'is_action' => true,
		'section' => 'action'
	]);
	
	$result[] = $item;

	$user_relationship = $event->getRelationshipByUser($user->getGUID());

	if ($user_relationship == EVENT_MANAGER_RELATION_ATTENDING_PENDING) {
		// resend confirmation
		$href = 'action/event_manager/event/resend_confirmation?guid=' . $event->getGUID() . '&user=' . $user->getGUID();

		$item = ElggMenuItem::factory([
			'name' => 'event_manager_resend_confirmation',
			'text' => elgg_echo("event_manager:event:menu:user_hover:resend_confirmation"),
			'href' => $href,
			'is_action' => true,
			'section' => 'action'
		]);
		
		$result[] = $item;
	}

	if (in_array($user_relationship, [EVENT_MANAGER_RELATION_ATTENDING_PENDING, EVENT_MANAGER_RELATION_ATTENDING_WAITINGLIST])) {
		// move to attendees
		$href = 'action/event_manager/attendees/move_to_attendees?guid=' . $event->getGUID() . '&user=' . $user->getGUID();
		
		$item = ElggMenuItem::factory([
			'name' => 'event_manager_move_to_attendees',
			'text' => elgg_echo("event_manager:event:menu:user_hover:move_to_attendees"),
			'href' => $href,
			'is_action' => true,
			'section' => 'action'
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
function event_manager_entity_menu($hook, $entity_type, $returnvalue, $params) {
	if (elgg_in_context('widgets')) {
		return;
	}
	
	$entity = elgg_extract('entity', $params);
	if (empty($entity) || !elgg_instanceof($entity, 'object', Event::SUBTYPE)) {
		return;
	}
	
	$result = $returnvalue;
		
	$attendee_count = $entity->countAttendees();
	if ($attendee_count > 0 || $entity->openForRegistration()) {
		$result[] = ElggMenuItem::factory([
			'name' => 'attendee_count',
			'priority' => 50,
			'text' => elgg_echo('event_manager:event:relationship:event_attending:entity_menu', [$attendee_count]),
			'href' => false
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
		$result[] = ElggMenuItem::factory([
			'name' => 'unsubscribe',
			'text' => elgg_echo('event_manager:menu:unsubscribe'),
			'href' => 'events/unsubscribe/' . $entity->getGUID() . '/' . elgg_get_friendly_title($entity->title),
			'priority' => 300
		]);
	}

	return $result;
}

/**
 * add menu item to owner block
 *
 * @param string $hook        hook name
 * @param string $entity_type hook type
 * @param array  $returnvalue current return value
 * @param array  $params      parameters
 * 
 * @return array
 */
function event_manager_owner_block_menu($hook, $entity_type, $returnvalue, $params) {

	if (empty($params) || !is_array($params)) {
		return;
	}

	$group = elgg_extract('entity', $params);
	if (empty($group) || !elgg_instanceof($group, 'group')) {
		return;
	}

	if (!event_manager_groups_enabled() || $group->event_manager_enable == 'no') {
		return;
	}

	$returnvalue[] = ElggMenuItem::factory([
		'name' => 'events',
		'text' => elgg_echo('event_manager:menu:group_events'),
		'href' => 'events/event/list/' . $group->getGUID()
	]);

	return $returnvalue;
}

/**
 * Generates correct title link for widgets depending on the context
 *
 * @param string $hook        hook name
 * @param string $entity_type hook type
 * @param array  $returnvalue current return value
 * @param array  $params      parameters
 * 
 * @return string
 */
function event_manager_widget_events_url($hook, $entity_type, $returnvalue, $params) {
	$result = $returnvalue;
	$widget = elgg_extract('entity', $params);

	if (empty($result) || !($widget instanceof ElggWidget) || $widget->handler !== 'events') {
		return;	
	}
		
	switch ($widget->context) {
		case 'index':
			return '/events';
		case 'groups':
			return '/events/event/list/' . $widget->getOwnerGUID();
	}
}

/**
 * Allow non user to remove their registration correctly
 *
 * @param string $hook        hook name
 * @param string $entity_type hook type
 * @param bool   $returnvalue current return value
 * @param array  $params      parameters
 * 
 * @return bool
 */
function event_manager_permissions_check_handler($hook, $entity_type, $returnvalue, $params) {
	global $EVENT_MANAGER_UNDO_REGISTRATION;
	$result = $returnvalue;

	// only override the hook if not already allowed
	if (!$result && !empty($params) && is_array($params)) {
		$entity = elgg_extract("entity", $params);

		if (elgg_instanceof($entity, "object", EventRegistration::SUBTYPE)) {
			if (!empty($EVENT_MANAGER_UNDO_REGISTRATION)) {
				$result = true;
			}
		}
	}

	return $result;
}

/**
 * Flushes simple cache after saving the settings
 *
 * @param string $hook        hook name
 * @param string $entity_type hook type
 * @param bool   $returnvalue current return value
 * @param array  $params      parameters
 * 
 * @return bool
 */
function event_manager_invalidate_cache($hook, $entity_type, $returnvalue, $params) {
	$plugin = elgg_extract('plugin', $params);
	if (empty($plugin)) {
		return;
	}
	
	if ($plugin->getID() !== 'event_manager') {
		return;
	}
	
	elgg_invalidate_simplecache();
}

/**
 * Prepare a notification message about a created event
 *
 * @param string                          $hook         Hook name
 * @param string                          $type         Hook type
 * @param Elgg_Notifications_Notification $notification The notification to prepare
 * @param array                           $params       Hook parameters
 * 
 * @return Elgg_Notifications_Notification
 */
function event_manager_prepare_notification($hook, $type, $notification, $params) {
	$entity = $params['event']->getObject();
	$owner = $params['event']->getActor();
	$language = $params['language'];
	
	$subject = elgg_echo('event_manager:notification:subject', array(), $language);
	$summary = elgg_echo('event_manager:notification:summary', array(), $language);

	$body = elgg_echo('event_manager:notification:body', array($owner->name, $entity->title), $language);

	if ($description = $entity->description) {
		$body .= PHP_EOL . PHP_EOL . elgg_get_excerpt($description);
	}

	$body .= PHP_EOL . PHP_EOL . $entity->getURL();

	$notification->subject = $subject;
	$notification->body = $body;
	$notification->summary = $summary;

	return $notification;
}