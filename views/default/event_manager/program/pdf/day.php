<?php

$day = elgg_extract('entity', $vars);
$register_type = elgg_extract('register_type', $vars);

if (!$day instanceof \ColdTrick\EventManager\Event\Day) {
	return;
}

$slots = '';
foreach ($day->getEventSlots() as $slot) {
	$slots .= elgg_view('event_manager/program/pdf/slot', [
		'entity' => $slot,
		'register_type' => $register_type,
		'user_guid' => elgg_extract('user_guid', $vars),
	]);
}

if (empty($slots)) {
	return;
}
	
$title = event_manager_format_date($day->date);
$description = $day->description;

if ($description) {
	$title = "{$description} ({$title})";
}

$result = elgg_format_element('div', [], $title);
if ($day->getDisplayName()) {
	$result .= elgg_format_element('div', [], $day->getDisplayName());
}

$result .= "<br /><br />{$slots}<br /><br />";

echo $result;
