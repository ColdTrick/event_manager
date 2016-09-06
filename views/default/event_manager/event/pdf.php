<?php

$event = elgg_extract('entity', $vars);
$owner = $event->getOwnerEntity();

if ($event->icontime) {
	$locator = new \Elgg\EntityDirLocator($event->guid);
	$entity_path = elgg_get_data_path() . $locator->getPath();
	
	$filename = $entity_path . "master.jpg";
	$filecontents = file_get_contents($filename);

	echo '<div class="mbm elgg-border-plain center"><img src="data:image/jpeg;base64,' . base64_encode($filecontents) . '" border="0" /></div>';
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

$when = "<div class='event-manager-event-when-title'>{$when_title}</div>";
if (!empty($when_subtitle)) {
	$when .= "<div class='event-manager-event-when-subtitle'>{$when_subtitle}</div>";
}

echo elgg_view_image_block(elgg_view_icon('calendar', ['class' => 'elgg-icon-hover']), $when, ['class' => 'event-manager-event-when']);

// event details
$location = $event->location;
if ($location) {
	echo '<label>' . elgg_echo('event_manager:edit:form:location') . '</label>';
	echo '<div class="mbm">' . $location . '</div>';
}

$organizer = $event->organizer;
if ($organizer) {
	echo '<label>' . elgg_echo('event_manager:edit:form:organizer') . '</label>';
	echo '<div class="mbm">' . $organizer . '</div>';
}

$description = $event->description;
if ($description) {
	echo '<label>' . elgg_echo('description') . '</label>';
	echo '<div class="mbm">' . $description . '</div>';
}

$region = $event->region;
if ($region) {
	echo '<label>' . elgg_echo('event_manager:edit:form:region') . '</label>';
	echo '<div class="mbm">' . $region . '</div>';
}

$type = $event->event_type;
if ($type) {
	echo '<label>' . elgg_echo('event_manager:edit:form:type') . '</label>';
	echo '<div class="mbm">' . $type . '</div>';
}
