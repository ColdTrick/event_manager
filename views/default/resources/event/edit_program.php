<?php

elgg_gatekeeper();

$guid = (int) elgg_extract('guid', $vars);
elgg_entity_gatekeeper($guid, 'object', Event::SUBTYPE);

$event = get_entity($guid);
if (!$event->canEdit()) {
	throw new \Elgg\EntityPermissionsException();
}

elgg_set_page_owner_guid($event->getContainerGUID());

elgg_push_entity_breadcrumbs($event);

// build page elements
$title_text = elgg_echo('event_manager:edit_program:title');

$content = elgg_format_element('div', [], elgg_view('output/longtext', [
	'value' => elgg_echo('event_manager:edit_program:description'),
	'class' => 'mbm',
]));

$content .= elgg_view('event_manager/program/view', ['entity' => $event]);

// build page data
$body = elgg_view_layout('default', [
	'filter' => false,
	'content' => $content,
	'title' => $title_text,
]);

// draw page
echo elgg_view_page($title_text, $body);
