<?php

$event = elgg_extract('entity', $vars);
$owner = $event->getOwnerEntity();

if ($event->icontime) {
	$locator = new \Elgg\EntityDirLocator($event->getOwnerGUID());
	$entity_path = elgg_get_data_path() . $locator->getPath();
	
	$filename = $entity_path . "events/{$event->guid}/master.jpg";
	$filecontents = file_get_contents($filename);

	echo '<div class="mbm elgg-border-plain center"><img src="data:image/jpeg;base64,' . base64_encode($filecontents) . '" border="0" /></div>';
}

$start_day = $event->start_day;
$start_time = $event->start_time;
$end_ts = $event->end_ts;

$when_title = elgg_echo('date:weekday:' . date('w', $start_day)) . ', ';
$when_title .= elgg_echo('date:month:' . date('m', $start_day), [date('j', $start_day)]) . ' ';
$when_title .= date('Y', $start_day);

$when_subtitle = '';

if (!$end_ts) {
	$when_title .= ' ' . date('H:i', $start_time);
} else {
	if (date('d-m-Y', $end_ts) === date('d-m-Y', $start_day)) {
		// same day event
		$when_subtitle .= date('H:i', $start_time) . ' ' . strtolower(elgg_echo('to')) . ' ' . date('H:i', $end_ts);
	} else {
		$when_title .= ' ' . date('H:i', $start_time);
		$when_subtitle .= strtolower(elgg_echo('to')) . ' ';

		$when_subtitle .= elgg_echo('date:weekday:' . date('w', $end_ts)) . ', ';
		$when_subtitle .= elgg_echo('date:month:' . date('m', $end_ts), [date('j', $end_ts)]) . ' ';
		$when_subtitle .= date('Y', $end_ts) . ' ';
		$when_subtitle .= date('H:i', $end_ts);
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
