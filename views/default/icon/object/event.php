<?php

$entity = elgg_extract('entity', $vars);
$size = elgg_extract('size', $vars, 'medium');

if ($size !== 'date') {
	return;
}

$event_start = $entity->getStartTimestamp();

$icon = "<div class='event_manager_event_list_icon' title='" . event_manager_format_date($event_start) . "'>";
$icon .= "<div class='event_manager_event_list_icon_month'>" . strtoupper(trim(elgg_echo('date:month:short:' . date("m", $event_start), ['']))) . "</div>";
$icon .= "<div class='event_manager_event_list_icon_day'>" . date("d", $event_start) . "</div>";
$icon .= "</div>";

echo $icon;
