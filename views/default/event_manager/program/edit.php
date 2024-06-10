<?php
// @todo merge this view with the event_manager/program/view view

$event = elgg_extract('entity', $vars);
$register_type = elgg_extract('register_type', $vars);

if (!$event instanceof \Event) {
	return;
}

if (!$event->with_program) {
	return;
}

$tab_options = [
	'id' => 'event_manager_event_view_program',
	'tabs' => [],
];

$eventDays = $event->getEventDays();
foreach ($eventDays as $key => $day) {
	$tab_options['tabs'][] = [
		'text' => $day->description ?: event_manager_format_date($day->date),
		'content' => elgg_view('event_manager/program/elements/day', [
			'entity' => $day,
			'participate' => true,
			'register_type' => $register_type,
		]),
		'selected' => ($key === 0),
	];
}

$program = elgg_view('input/hidden', [
	'id' => 'event_manager_program_guids',
	'name' => 'program_guids'
]);

$program .= elgg_view('page/components/tabs', $tab_options);

$slot_sets = elgg_get_metadata([
	'type' => 'object',
	'subtype' => \ColdTrick\EventManager\Event\Slot::SUBTYPE,
	'container_guids' => [$event->guid],
	'metadata_names' => ['slot_set'],
	'count' => true,
]);

if ($slot_sets > 0) {
	$program .= elgg_format_element('span', ['class' => 'elgg-subtext'], elgg_echo('event_manager:registration:slot_set:info'));
}

echo elgg_view_module('info', elgg_echo('event_manager:event:program'), $program);
