<?php

/* @var $widget ElggWidget */
$widget = elgg_extract('entity', $vars);

echo elgg_view('object/widget/edit/num_display', [
	'entity' => $widget,
	'default' => 5,
]);

if (in_array($widget->context, ['dashboard', 'profile'])) {
	echo elgg_view_field([
		'#type' => 'select',
		'#label' => elgg_echo('widgets:events:showevents'),
		'name' => 'params[type_to_show]',
		'value' => $widget->type_to_show,
		'options_values' => [
			'all' => elgg_echo('all'),
			'owning' => elgg_echo('widgets:events:showevents:icreated'),
			'attending' => elgg_echo('widgets:events:showevents:attendingto'),
		],
	]);
} else {
	echo elgg_view_field([
		'#type' => 'select',
		'#label' => elgg_echo('widgets:events:showevents:status'),
		'name' => 'params[event_status]',
		'value' => $widget->event_status,
		'options_values' => [
			'upcoming' => elgg_echo('event_manager:list:upcoming'),
			'live' => elgg_echo('event_manager:list:live'),
		],
	]);
}

if (!$widget->getOwnerEntity() instanceof ElggSite) {
	// profile, dashboard, groups
	return;
}

echo elgg_view_field([
	'#type' => 'grouppicker',
	'#label' => elgg_echo('widgets:events:group'),
	'name' => 'params[group_guid]',
	'values' => $widget->group_guid,
	'limit' => 1,
]);
