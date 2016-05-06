<?php

$day = elgg_extract('entity', $vars);
$participate = elgg_extract('participate', $vars);
$register_type = elgg_extract('register_type', $vars);

if (!($day instanceof \ColdTrick\EventManager\Event\Day)) {
	return;
}

$slots = '';
$daySlots = $day->getEventSlots();

if ($daySlots) {
	foreach ($daySlots as $slot) {
		$slots .= elgg_view('event_manager/program/pdf/slot', [
			'entity' => $slot,
			'participate' => $participate,
			'register_type' => $register_type,
			'user_guid' => $vars['user_guid'],
		]);
	}
}

if (empty($slots)) {
	return;
}
	
$title = event_manager_format_date($day->date);
$description = $day->description;

if ($description) {
	$title = "{$description} ({$title})";
}

$result = "<div>{$title}</div>";
if ($day->title) {
	$result .= "<div>{$day->title}</div>";
}

$result .= "<br /><br />{$slots}<br /><br />";

echo $result;
