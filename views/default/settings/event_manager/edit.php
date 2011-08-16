<?php 
	global $CONFIG;
	
	$who_create_site_events = (($vars['entity']->who_create_site_events != '')?$vars['entity']->who_create_site_events:'everyone');
	$who_create_group_events = (($vars['entity']->who_create_group_events != '')?$vars['entity']->who_create_group_events:'members');
	
	echo elgg_echo('event_manager:settings:enter_google_maps_key').elgg_view('input/text', array('internalname' => 'params[google_maps_key]', 'value' => $vars['entity']->google_maps_key)).'<br />';
	echo elgg_echo('event_manager:settings:enter_google_maps_key:clickhere').'<br /><br />';
	
	$google_maps_default_location = (($vars['entity']->google_maps_default_location != '')?$vars['entity']->google_maps_default_location:'Netherlands');
	
	echo elgg_echo('event_manager:settings:google_maps:enterdefaultlocation').'</label><br />';
	echo elgg_view('input/text', array('internalname' => 'params[google_maps_default_location]', 'value' => $google_maps_default_location)).'<br />';
	
	$google_maps_default_zoom = (($vars['entity']->google_maps_default_zoom != '')?$vars['entity']->google_maps_default_zoom:'10');
	
	echo elgg_echo('event_manager:settings:google_maps:enterdefaultzoom').'</label><br />';
	echo elgg_view('input/pulldown', array('internalname' => 'params[google_maps_default_zoom]', 'value' => $google_maps_default_zoom, 'options' => range(0, 19))).'<br />';
	
	echo elgg_echo('event_manager:settings:region_list').elgg_view('input/plaintext', array('internalname' => 'params[region_list]', 'value' => $vars['entity']->region_list)).'<br />';
	echo elgg_echo('event_manager:settings:type_list').elgg_view('input/plaintext', array('internalname' => 'params[type_list]', 'value' => $vars['entity']->type_list));
	
	echo elgg_echo('event_manager:settings:migration:site:whocancreate').'</label><br />';
	echo elgg_view('input/radio', array('internalname' => 'params[who_create_site_events]', 'value' => $who_create_site_events, 'options' => array(elgg_echo('event_manager:settings:migration:site:whocancreate:admin_only') => 'admin_only', elgg_echo('event_manager:settings:migration:site:whocancreate:everyone') => 'everyone')));
	
	echo elgg_echo('event_manager:settings:migration:group:whocancreate').'</label><br />';
	echo elgg_view('input/radio', array('internalname' => 'params[who_create_group_events]', 'value' => $who_create_group_events, 'options' => array(elgg_echo('event_manager:settings:migration:group:whocancreate:group_admin') => 'group_admin', elgg_echo('event_manager:settings:migration:group:whocancreate:members') => 'members', elgg_echo('event_manager:settings:migration:group:whocancreate:no_one') => '')));
	
	$migratable_events = event_manager_get_migratable_events();
	
	if($migratable_events['count'] > 0)
	{
		$migrate_url = elgg_add_action_tokens_to_url("/action/event_manager/migrate/calender");
		
		echo elgg_view('output/confirmlink', array('href' => $migrate_url, 'text' => sprintf(elgg_echo('event_manager:settings:migration'), $migratable_events['count'])));
	}