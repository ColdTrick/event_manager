<?php

// current value
$event_type = elgg_extract('event_type', $vars);

$type_settings = trim((string) elgg_get_plugin_setting('type_list', 'event_manager'));
$type_list = explode(',', $type_settings);

array_walk($type_list, function(&$val) {
	$val = trim($val);
});

$type_list = array_filter($type_list, function($value) {
	return !elgg_is_empty($value);
});

if (empty($type_list)) {
	if (!empty($region)) {
		echo elgg_view_field([
			'#type' => 'hidden',
			'name' => 'event_type',
			'value' => $event_type,
		]);
	}
	return;
}

$options = [
	'',
];

$options = array_merge($options, $type_list);
if (!in_array($event_type, $options)) {
	$options[] = $event_type;
}

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('event_manager:edit:form:type'),
	'#help' => elgg_echo('event_manager:edit:form:type:help'),
	'name' => 'event_type',
	'value' => $event_type,
	'options' => $options,
]);
