<?php
// @todo merge this view with the event_manager/program/view view

$event = elgg_extract('entity', $vars);
$register_type = elgg_extract('register_type', $vars);

if (empty($event) || !($event instanceof Event)) {
	return;
}

if (!$event->with_program) {
	return;
}

elgg_require_js('event_manager/view_event');

$tabtitles = '';
$tabcontent = '';

if ($eventDays = $event->getEventDays()) {
	foreach ($eventDays as $key => $day) {
		if ($key == 0) {
			// select the first
			$selected = true;
			$tabtitles .= "<li class='elgg-state-selected'>";
		} else {
			$selected = false;
			$tabtitles .= "<li>";
		}
		
		$day_title = event_manager_format_date($day->date);
		if ($description = $day->description) {
			$day_title = $description;
		}
		
		$tabtitles .= "<a href='javascript:void(0);' rel='day_" . $day->guid . "'>" . $day_title . "</a>";
		$tabtitles .= "</li>";
		
		$tabcontent .= elgg_view('event_manager/program/elements/day', [
			'entity' => $day,
			'selected' => $selected,
			'participate' => true,
			'register_type' => $register_type
		]);
	}
}

$program = '<div id="event_manager_event_view_program">';
$program .= '<ul class="elgg-tabs elgg-htabs">';

$program .= $tabtitles;

$program .= '</ul>';

$program .= '</div>';
$program .= elgg_view('input/hidden', [
	'id' => 'event_manager_program_guids',
	'name' => 'program_guids'
]);

$program .= $tabcontent;

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
