<?php

	// load google maps js
	global $EVENT_MANAGER_WIDGET_GOOGLE_JS_LOADED;
	if(empty($EVENT_MANAGER_WIDGET_GOOGLE_JS_LOADED) && event_manager_has_maps_key())
	{
		$EVENT_MANAGER_WIDGET_GOOGLE_JS_LOADED = true;
		elgg_extend_view("metatags", "event_manager/googlemapsjs");
	}
	
	// set default value
	if (!isset($vars['entity']->num_display)) {
		$vars['entity']->num_display = 5;
	}
	
	if (!isset($vars['entity']->type_to_show)) {
		$vars['entity']->type_to_show = 5;
	}
?>
<p>
<?php 
	echo elgg_echo('event_manager:widgets:events:numbertodisplay').':';
	echo elgg_view('input/text', array('internalname' => 'params[num_display]', 'value' => $vars['entity']->num_display));
?>
</p>
<p>
 <?php

if(in_array(get_context(), array('dashboard', 'profile')))
{
	echo 	elgg_echo('event_manager:widgets:events:showevents').':'.
			elgg_view('input/pulldown', array(	'internalname' => 'params[type_to_show]', 
												'options' => array(	'all' => elgg_echo('all'),
	 																'owning' => elgg_echo('event_manager:widgets:events:showevents:icreated'), 
	 																'attending' => elgg_echo('event_manager:widgets:events:showevents:attendingto'))));
}
?>
</p>