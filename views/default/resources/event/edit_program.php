<?php

$guid = (int) elgg_extract('guid', $vars);
elgg_entity_gatekeeper($guid, 'object', \Event::SUBTYPE, true);

/* @var $event \Event */
$event = get_entity($guid);

elgg_register_menu_item('title', [
	'name' => 'event',
	'href' => $event->getURL(),
	'icon_alt' => 'arrow-right',
	'text' => elgg_echo('event_manager:edit_program:continue'),
	'class' => ['elgg-button', 'elgg-button-action'],
]);

elgg_set_page_owner_guid($event->getContainerGUID());

elgg_push_entity_breadcrumbs($event);

$content = elgg_format_element('div', [], elgg_view('output/longtext', [
	'value' => elgg_echo('event_manager:edit_program:description'),
	'class' => 'mbm',
]));

$content .= elgg_view('event_manager/program/view', ['entity' => $event]);

echo elgg_view_page(elgg_echo('event_manager:edit_program:title'), [
	'content' => $content,
	'filter' => false,
]);
