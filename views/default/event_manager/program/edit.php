<?php
// @todo merge this view with the event_manager/program/view view

$event = elgg_extract('entity', $vars);
$register_type = elgg_extract('register_type', $vars);

if (!$event instanceof Event) {
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
if ($eventDays) {
	$member = elgg_extract('member', $vars);
	foreach ($eventDays as $key => $day) {
		$day_title = event_manager_format_date($day->date);
		if ($description = $day->description) {
			$day_title = $description;
		}
		
		$tab_options['tabs'][] = [
			'text' => $day_title,
			'content' => elgg_view('event_manager/program/elements/day', [
				'entity' => $day,
				'participate' => true,
				'register_type' => $register_type,
			]),
			'selected' => ($key === 0),
		];
	}
}

$program = elgg_view('input/hidden', [
	'id' => 'event_manager_program_guids',
	'name' => 'program_guids'
]);

$program .= elgg_view('page/components/tabs', $tab_options);

$slot_sets = elgg_get_metadata([
	'type' => 'object',
	'subtype' => \ColdTrick\EventManager\Event\Slot::SUBTYPE,
	'container_guids' => array($event->guid),
	'metadata_names' => ['slot_set'],
	'count' => true
]);

if ($slot_sets > 0) {
	$program .= '<span class="elgg-subtext">' . elgg_echo('event_manager:registration:slot_set:info') .  '</span>';
}

echo elgg_view_module('info', elgg_echo('event_manager:event:program'), $program);
