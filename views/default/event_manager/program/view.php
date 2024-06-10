<?php

$event = elgg_extract('entity', $vars);
if (!$event instanceof \Event) {
	return;
}

if (!$event->with_program) {
	return;
}

if ($event->canEdit()) {
	elgg_import_esm('event_manager/program/view');
}

$show_owner_actions = elgg_extract('show_owner_actions', $vars, true);

$tab_options = [
	'id' => 'event_manager_event_view_program',
	'tabs' => [],
];

$member = elgg_extract('member', $vars);
foreach ($event->getEventDays() as $key => $day) {
	$tab_options['tabs'][] = [
		'text' => $day->description ?: event_manager_format_date($day->date),
		'rel' => "day_{$day->guid}",
		'content' => elgg_view('event_manager/program/elements/day', [
			'entity' => $day,
			'member' => $member,
			'show_owner_actions' => $show_owner_actions,
		]),
		'selected' => ($key === 0),
	];
}

$module_vars = [];
if ($event->canEdit() && $show_owner_actions) {
	$module_vars['menu'] = elgg_view('output/url', [
		'href' => false,
		'rel' => $event->guid,
		'data-colorbox-opts' => json_encode([
			'href' => elgg_normalize_url('ajax/view/event_manager/forms/program/day?event_guid=' . $event->guid)
		]),
		'class' => 'event_manager_program_day_add elgg-lightbox',
		'text' => elgg_echo('event_manager:program:day:add'),
		'icon' => 'plus',
	]);
}

$content = elgg_view('page/components/tabs', $tab_options);

echo elgg_view_module('info', elgg_echo('event_manager:event:program'), $content, $module_vars);
