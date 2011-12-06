<?php 
	
	$plugin = $vars["entity"];
	
	
	$site_create_options = array(
		"everyone" => elgg_echo('event_manager:settings:migration:site:whocancreate:everyone'),
		"admin_only" => elgg_echo('event_manager:settings:migration:site:whocancreate:admin_only')
	);
	
	$group_create_options = array(
		"members" => elgg_echo('event_manager:settings:migration:group:whocancreate:members'), 
		"group_admin" => elgg_echo('event_manager:settings:migration:group:whocancreate:group_admin'), 
		"" => elgg_echo('event_manager:settings:migration:group:whocancreate:no_one')
	);
	
	$google_maps_default_location = $plugin->google_maps_default_location;
	
	if(empty($google_maps_default_location)){
		$google_maps_default_location = 'Netherlands';
	}
	
	$google_maps_default_zoom = (int) $plugin->google_maps_default_zoom;
	if($plugin->google_maps_default_zoom == ""){
		$google_maps_default_zoom = 10;
	}
	
	// Google Maps settings
	$maps = "<div>";
	$maps .= elgg_echo('event_manager:settings:enter_google_maps_key');
	$maps .= elgg_view('input/text', array('name' => 'params[google_maps_key]', 'value' => $plugin->google_maps_key));
	$maps .= "<br />";
	$maps .= elgg_echo('event_manager:settings:enter_google_maps_key:clickhere');
	$maps .= "</div>";
	
	$maps .= "<div>";
	$maps .= elgg_echo('event_manager:settings:google_maps:enterdefaultlocation');
	$maps .= "<br />";
	$maps .= elgg_view('input/text', array('name' => 'params[google_maps_default_location]', 'value' => $google_maps_default_location));
	$maps .= "</div>";
	
	$maps .= "<div>";
	$maps .= elgg_echo('event_manager:settings:google_maps:enterdefaultzoom');
	$maps .= "<br />";
	$maps .= elgg_view('input/dropdown', array('name' => 'params[google_maps_default_zoom]', 'value' => $google_maps_default_zoom, 'options' => range(0, 19)));
	$maps .= "</div>";
	
	echo elgg_view_module("info", elgg_echo("event_manager:settings:google_maps"), $maps);
	
	// Other settings
	echo "<div>";
	echo elgg_echo('event_manager:settings:region_list');
	echo elgg_view('input/plaintext', array('name' => 'params[region_list]', 'value' => $plugin->region_list));
	echo "</div>";
	
	echo "<div>";
	echo elgg_echo('event_manager:settings:type_list');
	echo elgg_view('input/plaintext', array('name' => 'params[type_list]', 'value' => $plugin->type_list));
	echo "</div>";
	
	echo "<div>";
	echo elgg_echo('event_manager:settings:migration:site:whocancreate');
	echo "&nbsp;" . elgg_view('input/dropdown', array('name' => 'params[who_create_site_events]', 'value' => $plugin->who_create_site_events, 'options_values' => $site_create_options));
	echo "</div>";
	
	echo "<div>";
	echo elgg_echo('event_manager:settings:migration:group:whocancreate');
	echo "&nbsp;" . elgg_view('input/dropdown', array('name' => 'params[who_create_group_events]', 'value' => $plugin->who_create_group_events, 'options_values' => $group_create_options));
	echo "</div>";
	
	$migratable_events = event_manager_get_migratable_events();
	
	if($migratable_events['count'] > 0)
	{
		$migrate_url = elgg_add_action_tokens_to_url("/action/event_manager/migrate/calender");
		echo "<div>";
		echo elgg_view('output/confirmlink', array('href' => $migrate_url, 'text' => elgg_echo('event_manager:settings:migration', array($migratable_events['count']))));
		echo "</div>";
	}
	
	echo "<div>";
	echo elgg_echo('event_manager:settings:notification_sender');
	echo "<br />";
	echo elgg_view('input/text', array('name' => 'params[notification_sender]', 'value' => $notification_sender));
	echo "</div>";
	