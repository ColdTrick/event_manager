<?php

$event = elgg_extract('entity', $vars);

if (!($event instanceof Event)) {
	return;
}

if (!$event->with_program) {
	return;
}

if ($event->canEdit()) {
	elgg_require_js('event_manager/edit_program');
}

$tab_options = [
	'id' => 'event_manager_event_view_program',
	'tabs' => [],
];

$eventDays = $event->getEventDays();
if ($eventDays) {
	$member = elgg_extract('member', $vars);
	foreach ($eventDays as $key => $day) {
		$day_title = event_manager_format_date($day->date);
		if ($description = $day->description) {
			$day_title = $description;
		}
		
		$tab_options['tabs'][] = [
			'text' => $day_title,
			'rel' => "day_{$day->guid}",
			'content' => elgg_view('event_manager/program/elements/day', [
				'entity' => $day,
				'member' => $member,
			]),
			'selected' => ($key === 0),
		];
	}
}

$module_vars = [];
if ($event->canEdit() && !elgg_in_context('programmailview')) {
	
	$module_vars['menu'] = elgg_view('output/url', [
		'href' => 'javascript:void(0);',
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
