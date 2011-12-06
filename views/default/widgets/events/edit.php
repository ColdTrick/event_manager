<?php
// set default value
if (!isset($vars['entity']->num_display)) {
	$vars['entity']->num_display = 5;
}

if (!isset($vars['entity']->type_to_show)) {
	$vars['entity']->type_to_show = 5;
}

echo "<div>"; 
echo elgg_echo('event_manager:widgets:events:numbertodisplay').':';
echo elgg_view('input/text', array('name' => 'params[num_display]', 'value' => $vars['entity']->num_display));
echo "</div>";

if(in_array(elgg_get_context(), array('dashboard', 'profile'))){
	echo "<div>";
	echo elgg_echo('event_manager:widgets:events:showevents').':';
	echo elgg_view('input/dropdown', array('name' => 'params[type_to_show]', 
										   'options' => array('all' => elgg_echo('all'),
	 														  'owning' => elgg_echo('event_manager:widgets:events:showevents:icreated'), 
	 														  'attending' => elgg_echo('event_manager:widgets:events:showevents:attendingto'))
														)
											);
	echo "</div>";
}
