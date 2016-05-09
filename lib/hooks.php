<?php
/**
 * Hook are bundled here
 */



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