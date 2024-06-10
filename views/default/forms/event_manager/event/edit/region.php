<?php

// current value
$region = elgg_extract('region', $vars);

$region_settings = trim((string) elgg_get_plugin_setting('region_list', 'event_manager'));
$region_list = explode(',', $region_settings);

array_walk($region_list, function(&$val) {
	$val = trim($val);
});

$region_list = array_filter($region_list, function($value) {
	return !elgg_is_empty($value);
});

if (empty($region_list)) {
	if (!empty($region)) {
		echo elgg_view_field([
			'#type' => 'hidden',
			'name' => 'region',
			'value' => $region,
		]);
	}
	
	return;
}

$options = array_merge([''], $region_list);
if (!in_array($region, $options)) {
	$options[] = $region;
}

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('event_manager:edit:form:region'),
	'#help' => elgg_echo('event_manager:edit:form:region:help'),
	'name' => 'region',
	'value' => $region,
	'options' => $options,
]);
