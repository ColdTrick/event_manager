<?php

$result = elgg_view('event_manager/event_sort_menu');

$options = array(
	"count" => elgg_extract("count", $vars),
	"offset" => elgg_extract("offset", $vars),
	"full_view" => false,
	"pagination" => false
);

$list = elgg_view_entity_list($vars["entities"], $options);

$result .= "<div id='event_manager_event_listing'>";
if (!empty($list)) {
	$result .= $list;
} else {
	$result .= elgg_echo('event_manager:list:noresults');
}
$result .= "</div>";

$result .= elgg_view("event_manager/onthemap", $vars);

$limit = elgg_extract("limit", $vars, 10);

if ($vars["count"] > $limit) {
	$result .= '<div id="event_manager_event_list_search_more" rel="'. ((isset($vars["offset"])) ? $vars["offset"] : $limit).'">';
	$result .= elgg_echo('event_manager:list:showmorevents');
	$result .= ' (' . ($vars["count"] - ($offset + $limit)) . ')</div>';
}

echo elgg_view_module("main", "", $result);
