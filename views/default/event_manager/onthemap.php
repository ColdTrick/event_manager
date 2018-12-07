<?php

$maps_provider = elgg_get_plugin_setting('maps_provider', 'event_manager', 'google');
if ($maps_provider === 'none') {
	return;
}

if (elgg_view_exists("event_manager/maps/{$maps_provider}/onthemap.js")) {
	elgg_require_js("event_manager/maps/{$maps_provider}/onthemap");
}

$body = '<div id="event_manager_onthemap_canvas"></div>';

$legend = elgg_view("event_manager/maps/{$maps_provider}/legend");
if (!empty($legend)) {
	$body .= elgg_format_element('div', ['id' => 'event_manager_onthemap_legend'], $legend);
}

echo elgg_format_element('div', ['id' => 'event_manager_event_map'], $body);

echo elgg_format_element('script', [], 'require(["elgg"], function(elgg) {
	elgg.trigger_hook("tab:onthemap", "event_manager");
});');
