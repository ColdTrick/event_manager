<?php

$widget = elgg_extract('entity', $vars);

$num_display = (int) $widget->num_display;
$type_to_show = $widget->type_to_show;

// set default value
if ($num_display < 1) {
	$num_display = 5;
}

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('event_manager:widgets:events:numbertodisplay'),
	'name' => 'params[num_display]',
	'value' => $num_display,
]);

if (in_array($widget->context, ['dashboard', 'profile'])) {
	echo elgg_view_field([
		'#type' => 'select',
		'#label' => elgg_echo('event_manager:widgets:events:showevents'),
		'name' => 'params[type_to_show]',
		'value' => $type_to_show,
		'options_values' => [
			'all' => elgg_echo('all'),
			'owning' => elgg_echo('event_manager:widgets:events:showevents:icreated'),
			'attending' => elgg_echo('event_manager:widgets:events:showevents:attendingto'),
		],
	]);
}

if (!($widget->getOwnerEntity() instanceof ElggSite)) {
	return;
}

$group_guid = $widget->group_guid;

if (elgg_view_exists('input/grouppicker')) {
	if (!empty($group_guid) && !is_array($group_guid)) {
		$group_guid = [$group_guid];
	}
	
	echo elgg_view_field([
		'#type' => 'hidden',
		'name' => 'params[group_guid]',
		'value' => 0,
	]);
	echo elgg_view_field([
		'#type' => 'grouppicker',
		'#label' => elgg_echo('event_manager:widgets:events:group'),
		'name' => 'params[group_guid]',
		'values' => $group_guid,
		'limit' => 1,
	]);
} else {
	echo elgg_view_field([
		'#type' => 'text',
		'#label' => elgg_echo('event_manager:widgets:events:group_guid'),
		'name' => 'params[group_guid]',
		'value' => $group_guid,
	]);
}