<?php

$guid = (int) elgg_extract('guid', $vars);

elgg_entity_gatekeeper($guid, 'object', Event::SUBTYPE);

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

elgg_set_page_owner_guid($event->getContainerGUID());
$page_owner = elgg_get_page_owner_entity();
if ($page_owner instanceof ElggGroup) {
	elgg_entity_gatekeeper($page_owner->guid);
}

elgg_push_entity_breadcrumbs($event, false);

$title_text = $event->getDisplayName();

$body = elgg_view_layout('default', [
	'filter' => false,
	'content' => elgg_view_entity($event),
	'title' => $title_text,
	'sidebar' => elgg_view('event_manager/event/sidebar', ['entity' => $event]),
	'entity' => $event,
]);

echo elgg_view_page($title_text, $body, 'default');
