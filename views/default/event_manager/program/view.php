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
elgg_require_js('event_manager/view_event');

$tabtitles = '';
$tabcontent = '';
$eventDays = $event->getEventDays();
if ($eventDays) {
	$member = elgg_extract('member', $vars);
	foreach ($eventDays as $key => $day) {
		$day_title = event_manager_format_date($day->date);
		if ($description = $day->description) {
			$day_title = $description;
		}
		
		$link = elgg_view('output/url', [
			'href' => 'javascript:void(0);',
			'rel' => 'day_' . $day->guid,
			'text' => $day_title
		]);
		
		$li_attrs = [];
		if ($key == 0) {
			$li_attrs['class'] = 'elgg-state-selected';
		}
		$tabtitles .= elgg_format_element('li', $li_attrs, $link);
		
		$tabcontent .= elgg_view('event_manager/program/elements/day', [
			'entity' => $day,
			'selected' => ($key === 0),
			'member' => $member,
		]);
	}
}

if ($event->canEdit() && !elgg_in_context('programmailview')) {

	elgg_load_js('lightbox');
	elgg_load_css('lightbox');
	
	$add_day = elgg_view('output/url', [
		'href' => 'javascript:void(0);',
		'rel' => $event->guid,
		'data-colorbox-opts' => json_encode([
			'href' => elgg_normalize_url('ajax/view/event_manager/forms/program/day?event_guid=' . $event->guid)
		]),
		'class' => 'event_manager_program_day_add elgg-lightbox',
		'text' => elgg_echo('event_manager:program:day:add')
	]);
	
	$tabtitles .= elgg_format_element('li', [], $add_day);
}

// make program
$tabs = elgg_format_element('ul', ['class' => 'elgg-tabs elgg-htabs'], $tabtitles);

$program = elgg_format_element('div', ['id' => 'event_manager_event_view_program'], $tabs);
$program .= $tabcontent;

echo elgg_view_module('info', elgg_echo('event_manager:event:program'), $program);
