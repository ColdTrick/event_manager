<?php

$plugin = elgg_extract('entity', $vars);
if (!$plugin instanceof \ElggPlugin) {
	return;
}

elgg_require_js('plugins/event_manager/settings');

$maps_provider = $plugin->maps_provider;

$maps = elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('event_manager:settings:maps:provider'),
	'#help' => elgg_echo('event_manager:settings:maps:provider:help'),
	'name' => 'params[maps_provider]',
	'value' => $maps_provider,
	'options_values' => [
		'none' => elgg_echo('event_manager:settings:maps:provider:none'),
		'google' => elgg_echo('event_manager:settings:maps:provider:google'),
		'osm' => elgg_echo('event_manager:settings:maps:provider:osm'),
	],
]);

$maps .= elgg_view_field([
	'#type' => 'fieldset',
	'id' => 'event-manager-maps-provider-google',
	'class' => [
		'event-manager-maps-provider',
		($maps_provider == 'google') ? '' : 'hidden',
	],
	'legend' => elgg_echo('event_manager:settings:google_maps'),
	'fields' => [
		[
			'#type' => 'text',
			'#label' => elgg_echo('event_manager:settings:google_api_key'),
			'name' => 'params[google_api_key]',
			'value' => $plugin->google_api_key,
			'help' => elgg_echo('event_manager:settings:google_api_key:clickhere')
		],
		[
			'#type' => 'text',
			'#label' => elgg_echo('event_manager:settings:google_maps:enterdefaultlocation'),
			'name' => 'params[google_maps_default_location]',
			'value' => $plugin->google_maps_default_location,
		],
		[
			'#type' => 'select',
			'#label' => elgg_echo('event_manager:settings:google_maps:enterdefaultzoom'),
			'name' => 'params[google_maps_default_zoom]',
			'value' => (int) $plugin->google_maps_default_zoom,
			'options' => range(0, 19),
		],
		[
			'#type' => 'select',
			'#label' => elgg_echo('event_manager:settings:google_maps:google_maps_detail_zoom'),
			'name' => 'params[google_maps_detail_zoom]',
			'value' => (int) $plugin->google_maps_detail_zoom,
			'options' => range(0, 19),
		],
	],
]);

$maps .= elgg_view_field([
	'#type' => 'fieldset',
	'id' => 'event-manager-maps-provider-osm',
	'class' => [
		'event-manager-maps-provider',
		($maps_provider == 'osm') ? '' : 'hidden',
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
	'options_values' => [
		'everyone' => elgg_echo('event_manager:settings:migration:site:whocancreate:everyone'),
		'admin_only' => elgg_echo('event_manager:settings:migration:site:whocancreate:admin_only'),
	],
]);

$other .= elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('event_manager:settings:migration:group:whocancreate'),
	'name' => 'params[who_create_group_events]',
	'value' => $plugin->who_create_group_events,
	'options_values' => [
		'members' => elgg_echo('event_manager:settings:migration:group:whocancreate:members'),
		'group_admin' => elgg_echo('event_manager:settings:migration:group:whocancreate:group_admin'),
		'' => elgg_echo('event_manager:settings:migration:group:whocancreate:no_one'),
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

echo elgg_view_module('info', elgg_echo('event_manager:settings:other'), $other);
