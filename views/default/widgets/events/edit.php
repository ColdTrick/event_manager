<?php
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