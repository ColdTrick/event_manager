<?php

$slot = elgg_extract('entity', $vars);
if (!$slot instanceof \ColdTrick\EventManager\Event\Slot) {
	return;
}

$user_guid = (int) elgg_extract('user_guid', $vars, elgg_get_logged_in_user_guid());
$user = get_entity($user_guid);
if (!$user instanceof \ElggEntity) {
	return;
}

if (!$user->hasRelationship($slot->guid, EVENT_MANAGER_RELATION_SLOT_REGISTRATION)) {
	return;
}

$start_time = $slot->start_time;
$end_time = $slot->end_time;

$result = "<table><tr><td style='padding-left: 10px; vertical-align: top'>";
$result .= date('H:i', $start_time) . ' - ' . date('H:i', $end_time);
$result .= '</td><td>';
$result .= elgg_format_element('b', [], $slot->getDisplayName());

if ($slot->location) {
	$result .= elgg_format_element('div', [], $slot->location);
}

$result .= elgg_format_element('div', [], elgg_view('output/text', ['value' => $slot->description]));

$result .= '</td></tr></table>';

echo $result;
