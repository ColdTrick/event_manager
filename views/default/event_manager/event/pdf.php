<?php

$event = elgg_extract('entity', $vars);

if ($event->hasIcon('header', 'header')) {
	$header_icon = $event->getIcon('header', 'header');
	
	$filecontents = $header_icon->grabFile();

	echo '<div class="mbm elgg-border-plain center"><img style="width: 100%;" src="data:image/jpeg;base64,' . base64_encode($filecontents) . '" border="0" /></div>';
}

$event_start = $event->getStartTimestamp();
$event_end = $event->getEndTimestamp();

$when_title = elgg_echo('date:weekday:' . gmdate('w', $event_start)) . ', ';
$when_title .= elgg_echo('date:month:' . gmdate('m', $event_start), [gmdate('j', $event_start)]) . ' ';
$when_title .= gmdate('Y', $event_start);

$when_subtitle = '';

if (!$event_end) {
	$when_title .= ' ' . gmdate('H:i', $event_start);
} else {
	if (gmdate('d-m-Y', $event_end) === gmdate('d-m-Y', $event_start)) {
		// same day event
		$when_subtitle .= gmdate('H:i', $event_start) . ' ' . strtolower(elgg_echo('event_manager:date:to')) . ' ' . gmdate('H:i', $event_end);
	} else {
		$when_title .= ' ' . gmdate('H:i', $event_start);
		$when_subtitle .= strtolower(elgg_echo('event_manager:date:to')) . ' ';

		$when_subtitle .= elgg_echo('date:weekday:' . gmdate('w', $event_end)) . ', ';
		$when_subtitle .= elgg_echo('date:month:' . gmdate('m', $event_end), [gmdate('j', $event_end)]) . ' ';
		$when_subtitle .= gmdate('Y', $event_end) . ' ';
		$when_subtitle .= gmdate('H:i', $event_end);
	}
}

$when = elgg_format_element('div', ['class' => 'event-manager-event-when-title'], $when_title);
if (!empty($when_subtitle)) {
	$when .= elgg_format_element('div', ['class' => 'event-manager-event-when-subtitle'], $when_subtitle);
}

echo elgg_view_image_block(elgg_view_icon('calendar', ['class' => 'elgg-icon-hover']), $when, ['class' => 'event-manager-event-when']);

// event details
$location = $event->location;
if ($location) {
	echo elgg_format_element('label', [], elgg_echo('event_manager:edit:form:location'));
	echo elgg_format_element('div', ['class' => 'mbm'], $location);
}

$organizer = $event->organizer;
if ($organizer) {
	echo elgg_format_element('label', [], elgg_echo('event_manager:edit:form:organizer'));
	echo elgg_format_element('div', ['class' => 'mbm'], $organizer);
}

$description = $event->description;
if ($description) {
	echo elgg_format_element('label', [], elgg_echo('description'));
	echo elgg_format_element('div', ['class' => 'mbm'], elgg_view('output/longtext', ['value' => $description]));
}

$region = $event->region;
if ($region) {
	echo elgg_format_element('label', [], elgg_echo('event_manager:edit:form:region'));
	echo elgg_format_element('div', ['class' => 'mbm'], $region);
}

$type = $event->event_type;
if ($type) {
	echo elgg_format_element('label', [], elgg_echo('event_manager:edit:form:type'));
	echo elgg_format_element('div', ['class' => 'mbm'], $type);
}
