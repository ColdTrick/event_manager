<?php

$entity = elgg_extract('entity', $vars);
$size = elgg_extract('size', $vars, 'medium');

if ($size !== 'date') {
	return;
}

$start_day = $entity->start_day;

$icon = "<div class='event_manager_event_list_icon' title='" . event_manager_format_date($start_day) . "'>";
$icon .= "<div class='event_manager_event_list_icon_month'>" . strtoupper(date("M", $start_day)) . "</div>";
$icon .= "<div class='event_manager_event_list_icon_day'>" . date("d", $start_day) . "</div>";
$icon .= "</div>";

echo $icon;
