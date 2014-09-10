<?php

$widget = $vars["entity"];

$num_display = (int) $widget->num_display;
$type_to_show = $widget->type_to_show;

// set default value
if ($num_display < 1) {
	$num_display = 5;
}
	
echo "<p>"; 
echo elgg_echo('event_manager:widgets:events:numbertodisplay') . ':';
echo elgg_view('input/text', array('name' => 'params[num_display]', 'value' => $num_display));
echo "</p>";

if (in_array($widget->context, array('dashboard', 'profile'))) {
	echo "<p>";
	echo elgg_echo('event_manager:widgets:events:showevents') . ': ';
	echo elgg_view('input/dropdown', array(
		'name' => 'params[type_to_show]', 
		'value' => $type_to_show, 
		'options_values' => array(
			'all' => elgg_echo('all'),
			'owning' => elgg_echo('event_manager:widgets:events:showevents:icreated'), 
			'attending' => elgg_echo('event_manager:widgets:events:showevents:attendingto')
		)
	));
	echo "</p>";
}

if ($widget->getOwnerEntity() instanceof ElggSite) {
	$group_guid = $widget->group_guid;
	
	if (elgg_view_exists("input/grouppicker")) {
		if (!empty($group_guid) && !is_array($group_guid)) {
			$group_guid = array($group_guid);
		}
		echo elgg_echo("event_manager:widgets:events:group") . ":";
		echo elgg_view("input/hidden", array("name" => "params[group_guid]", "value" => 0));
		echo elgg_view("input/grouppicker", array(
			"name" => "params[group_guid]",
			"values" => $group_guid,
			"limit" => 1
		));
	} else {
		echo elgg_echo("event_manager:widgets:events:group_guid") . ":";
		echo elgg_view("input/text", array(
			"name" => "params[group_guid]",
			"value" => $group_guid
		));
	}
}
