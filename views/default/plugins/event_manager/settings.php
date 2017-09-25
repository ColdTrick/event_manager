<?php
	
$plugin = elgg_extract('entity', $vars);

$site_create_options = [
	'everyone' => elgg_echo('event_manager:settings:migration:site:whocancreate:everyone'),
	'admin_only' => elgg_echo('event_manager:settings:migration:site:whocancreate:admin_only'),
];

$group_create_options = [
	'members' => elgg_echo('event_manager:settings:migration:group:whocancreate:members'),
	'group_admin' => elgg_echo('event_manager:settings:migration:group:whocancreate:group_admin'),
	'' => elgg_echo('event_manager:settings:migration:group:whocancreate:no_one'),
];

$yes_no_options = [
	'yes' => elgg_echo('option:yes'),
	'no' => elgg_echo('option:no'),
];

$google_maps_default_location = $plugin->google_maps_default_location;

if (empty($google_maps_default_location)) {
	$google_maps_default_location = 'Netherlands';
}

$google_maps_default_zoom = (int) $plugin->google_maps_default_zoom;
if ($plugin->google_maps_default_zoom == "") {
	$google_maps_default_zoom = 10;
}

// Google MAPS
$maps = elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('event_manager:settings:google_api_key'),
	'name' => 'params[google_api_key]',
	'value' => $plugin->google_api_key,
	'help' => elgg_echo('event_manager:settings:google_api_key:clickhere')
]);

$maps .= elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('event_manager:settings:google_maps:enterdefaultlocation'),
	'name' => 'params[google_maps_default_location]',
	'value' => $google_maps_default_location
]);

$maps .= elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('event_manager:settings:google_maps:enterdefaultzoom'),
	'name' => 'params[google_maps_default_zoom]',
	'value' => $google_maps_default_zoom,
	'options' => range(0, 19),
]);

echo elgg_view_module('inline', elgg_echo('event_manager:settings:google_maps'), $maps);

// Other settings
$other = elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('event_manager:settings:region_list'),
	'name' => 'params[region_list]',
	'value' => $plugin->region_list,
]);

$other .= elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('event_manager:settings:type_list'),
	'name' => 'params[type_list]',
	'value' => $plugin->type_list,
]);

$other .= elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('event_manager:settings:migration:site:whocancreate'),
	'name' => 'params[who_create_site_events]',
	'value' => $plugin->who_create_site_events,
	'options_values' => $site_create_options,
]);

$other .= elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('event_manager:settings:migration:group:whocancreate'),
	'name' => 'params[who_create_group_events]',
	'value' => $plugin->who_create_group_events,
	'options_values' => $group_create_options,
]);

$other .= elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('event_manager:settings:rsvp:interested'),
	'name' => 'params[rsvp_interested]',
	'value' => $plugin->rsvp_interested,
	'options_values' => $yes_no_options,
]);

$other .= elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('event_manager:settings:notification_sender'),
	'name' => 'params[notification_sender]',
	'value' => $plugin->notification_sender,
]);

$other .= elgg_vieW_field([
	'#type' => 'select',
	'#label' => elgg_echo('event_manager:settings:add_event_to_calendar'),
	'#help' => elgg_echo('event_manager:settings:add_event_to_calendar:help'),
	'name' => 'params[add_event_to_calendar]',
	'value' => $plugin->add_event_to_calendar,
	'options_values' => $yes_no_options,
]);

echo elgg_view_module('inline', elgg_echo('event_manager:settings:other'), $other);
