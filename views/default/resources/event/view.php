<?php

$guid = (int) elgg_extract('guid', $vars);

elgg_entity_gatekeeper($guid, 'object', Event::SUBTYPE);

/* @var $event Event */
$event = get_entity($guid);

if (elgg_get_plugin_setting('add_event_to_calendar', 'event_manager') === 'yes') {
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
]);
