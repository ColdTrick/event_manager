<?php

$guid = (int) elgg_extract('guid', $vars);

elgg_entity_gatekeeper($guid, 'object', Event::SUBTYPE);

/* @var $event Event */
$event = get_entity($guid);

$show_add_to_calendar = elgg_get_plugin_setting('add_event_to_calendar', 'event_manager');
if ($show_add_to_calendar === 'yes' || ($show_add_to_calendar === 'attendee_only' && !empty($event->getRelationshipByUser()))) {
	elgg_register_menu_item('title', ElggMenuItem::factory([
		'name' => 'addthisevent',
		'href' => false,
		'icon' => 'calendar',
		'class' => 'elgg-button elgg-button-action',
		'text' => elgg_view('event_manager/event/addthisevent', ['entity' => $event]),
		'deps' => 'addthisevent',
	]));
}

elgg_push_entity_breadcrumbs($event, false);

echo elgg_view_page($event->getDisplayName(), [
	'content' => elgg_view_entity($event),
	'sidebar' => elgg_view('event_manager/event/sidebar', ['entity' => $event]),
	'entity' => $event,
	'filter_id' => 'event/view',
]);
