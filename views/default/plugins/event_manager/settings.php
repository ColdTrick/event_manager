<?php

$plugin = elgg_extract('entity', $vars);
if (!$plugin instanceof \ElggPlugin) {
	return;
}

// make sure cache is flushed after saving new settings
echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'flush_cache',
	'value' => 1,
]);

// Maps settings
elgg_require_js('plugins/event_manager/settings');

$maps_provider = event_manager_get_maps_provider();

$maps = elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('event_manager:settings:maps:provider'),
	'#help' => elgg_echo('event_manager:settings:maps:provider:help'),
	'name' => 'params[maps_provider]',
	'value' => $maps_provider,
	'options_values' => [
		'none' => elgg_echo('event_manager:settings:maps:provider:none'),
		'osm' => elgg_echo('event_manager:settings:maps:provider:osm'),
	],
]);

$maps .= elgg_view_field([
	'#type' => 'fieldset',
	'#class' => [
		'event-manager-maps-provider',
		'event-manager-maps-provider-osm',
		($maps_provider === 'osm') ? '' : 'hidden',
	],
	'legend' => elgg_echo('event_manager:settings:osm'),
	'fields' => [
		[
			'#type' => 'text',
			'#label' => elgg_echo('event_manager:settings:osm:osm_default_location'),
			'name' => 'params[osm_default_location]',
			'value' => $plugin->osm_default_location,
		],
		[
			'#type' => 'text',
			'#label' => elgg_echo('event_manager:settings:osm:osm_default_location_lat'),
			'name' => 'params[osm_default_location_lat]',
			'value' => $plugin->osm_default_location_lat,
		],
		[
			'#type' => 'text',
			'#label' => elgg_echo('event_manager:settings:osm:osm_default_location_lng'),
			'name' => 'params[osm_default_location_lng]',
			'value' => $plugin->osm_default_location_lng,
		],
		[
			'#type' => 'select',
			'#label' => elgg_echo('event_manager:settings:osm:osm_default_zoom'),
			'name' => 'params[osm_default_zoom]',
			'value' => (int) $plugin->osm_default_zoom,
			'options' => range(0, 19),
		],
		[
			'#type' => 'select',
			'#label' => elgg_echo('event_manager:settings:osm:osm_detail_zoom'),
			'name' => 'params[osm_detail_zoom]',
			'value' => (int) $plugin->osm_detail_zoom,
			'options' => range(0, 19),
		],
	],
]);

echo elgg_view_module('info', elgg_echo('event_manager:settings:maps'), $maps);

// AddEvent options
$add_event = elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('event_manager:settings:add_event_to_calendar'),
	'#help' => elgg_echo('event_manager:settings:add_event_to_calendar:help'),
	'name' => 'params[add_event_to_calendar]',
	'value' => $plugin->add_event_to_calendar,
	'options_values' => [
		'no' => elgg_echo('option:no'),
		'yes' => elgg_echo('option:yes'),
		'attendee_only' => elgg_echo('event_manager:settings:add_event_to_calendar:attendee_only'),
	],
]);

$add_event .= elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('event_manager:settings:add_event_license'),
	'#help' => elgg_echo('event_manager:settings:add_event_license:help'),
	'name' => 'params[add_event_license]',
	'value' => $plugin->add_event_license,
]);

$services = [
	'google' => 'Google <em>(online)</em>',
	'yahoo' => 'Yahoo <em>(online)</em>',
	'office365' => 'Office 365 <em>(online)</em>',
	'outlookcom' => 'Outlook.com <em>(online)</em>',
	'outlook' => 'Outlook',
	'appleical' => 'iCal Calendar',
];

foreach ($services as $service => $label) {
	$service_setting = "show_service_{$service}";
	$add_event .= elgg_view_field([
		'#type' => 'checkbox',
		'#label' => elgg_echo('event_manager:settings:add_event:service', [$label]),
		'name' => "params[{$service_setting}]",
		'checked' => (bool) $plugin->{$service_setting},
		'switch' => true,
		'default' => 0,
		'value' => 1,
	]);
}

echo elgg_view_module('info', elgg_echo('event_manager:settings:add_event'), $add_event);


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
	'#label' => elgg_echo('event_manager:settings:site:whocancreate'),
	'name' => 'params[who_create_site_events]',
	'value' => $plugin->who_create_site_events,
	'options_values' => [
		'everyone' => elgg_echo('event_manager:settings:site:whocancreate:everyone'),
		'admin_only' => elgg_echo('event_manager:settings:site:whocancreate:admin_only'),
	],
]);

$other .= elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('event_manager:settings:group:whocancreate'),
	'name' => 'params[who_create_group_events]',
	'value' => $plugin->who_create_group_events,
	'options_values' => [
		'members' => elgg_echo('event_manager:settings:group:whocancreate:members'),
		'group_admin' => elgg_echo('event_manager:settings:group:whocancreate:group_admin'),
		'' => elgg_echo('event_manager:settings:group:whocancreate:no_one'),
	],
]);

$other .= elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('event_manager:settings:rsvp:interested'),
	'name' => 'params[rsvp_interested]',
	'checked' => $plugin->rsvp_interested === 'yes',
	'switch' => true,
	'default' => 'no',
	'value' => 'yes',
]);

$other .= elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('event_manager:settings:notification_sender'),
	'name' => 'params[notification_sender]',
	'value' => $plugin->notification_sender,
]);

$other .= elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('event_manager:settings:event_mail'),
	'#help' => elgg_echo('event_manager:settings:event_mail:help'),
	'name' => 'params[event_mail]',
	'checked' => (bool) $plugin->event_mail,
	'switch' => true,
	'default' => 0,
	'value' => 1,
]);

$other .= elgg_view_field([
	'#type' => 'number',
	'#label' => elgg_echo('event_manager:settings:announcement_period'),
	'#help' => elgg_echo('event_manager:settings:announcement_period:help'),
	'name' => 'params[announcement_period]',
	'value' => $plugin->announcement_period,
	'min' => 0,
]);

echo elgg_view_module('info', elgg_echo('event_manager:settings:other'), $other);
