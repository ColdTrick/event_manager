<?php

$region_options = event_manager_event_region_options();
$type_options = event_manager_event_type_options();

$form_body = elgg_view('output/url', [
	'href' => 'javascript: void(0);',
	'id' => 'event_manager_event_search_advanced_enable',
	'class' => 'elgg-button elgg-button-action float-alt',
	'rel' => 'toggle',
	'data-toggle-selector' => '#event-manager-search-form-past-events, #event_manager_event_search_advanced_container, #event_manager_event_search_advanced_enable span',
	'text' => '<span data-toggle-slide="0">' . elgg_echo('event_manager:list:advancedsearch') . '</span><span class="hidden" data-toggle-slide="0">' . elgg_echo('event_manager:list:simplesearch') . '</span>',
]);

$form_body .= elgg_view('input/hidden', ['name' => 'search_type', 'id' => 'search_type', 'value' => 'list']);
$form_body .= elgg_view('input/hidden', ['name' => 'latitude', 'id' => 'latitude']);
$form_body .= elgg_view('input/hidden', ['name' => 'longitude', 'id' => 'longitude']);
$form_body .= elgg_view('input/hidden', ['name' => 'distance_longitude', 'id' => 'distance_longitude']);
$form_body .= elgg_view('input/hidden', ['name' => 'distance_latitude', 'id' => 'distance_latitude']);
$form_body .= elgg_view('input/hidden', ['name' => 'container_guid', 'value' => elgg_get_page_owner_guid()]);
$form_body .= elgg_view('input/text', ['name' => 'search', 'id' => 'search', 'class' => 'event_manager_event_list_search_input mrs']);
$form_body .= elgg_view('input/submit', ['value' => elgg_echo('search')]);

$form_body .= '<div id="event_manager_event_search_advanced_container" class="mtm mbn hidden">';
$form_body .= elgg_view('input/hidden', [
	'name' => 'advanced_search',
	'id' => 'advanced_search',
	'value' => 0,
]);

$col_class = 'elgg-col';
$col_count = 1;
if (elgg_is_logged_in()) {
	$col_count++;
}
if ($region_options || $type_options) {
	$col_count++;
}
if ($col_count > 1) {
	$col_class .= ' elgg-col-1of' . $col_count;
}

$form_body .= "<div class='{$col_class}'>";

$form_body .= elgg_view_field([
	'#type' => 'date',
	'#label' => elgg_echo('event_manager:edit:form:start_day:from'),
	'name' => 'event_start',
	'timestamp' => true,
	'id' => 'event_start',
]);
$form_body .= elgg_view_field([
	'#type' => 'date',
	'#label' => elgg_echo('event_manager:edit:form:start_day:to'),
	'name' => 'event_end',
	'timestamp' => true,
	'id' => 'event_end',
]);

$form_body .= '</div>';

if ($region_options || $type_options) {
	$form_body .= "<div class='{$col_class}'>";

	if ($region_options) {
		$form_body .= elgg_view_field([
			'#type' => 'select',
			'#label' => elgg_echo('event_manager:edit:form:region'),
			'name' => 'region',
			'options' => $region_options,
		]);
	}

	if ($type_options) {
		$form_body .= elgg_view_field([
			'#type' => 'select',
			'#label' => elgg_echo('event_manager:edit:form:type'),
			'name' => 'event_type',
			'options' => $type_options,
		]);
	}

	$form_body .= '</div>';
}

if (elgg_is_logged_in()) {
	$form_body .= "<div class='{$col_class}'>";
	foreach (['attending', 'owning', 'friendsattending'] as $relationship) {
		$form_body .= elgg_view_field([
			'#type' => 'checkboxes',
			'id' => $relationship,
			'name' => $relationship,
			'value' => 0,
			'options' => [elgg_echo("event_manager:list:{$relationship}") => '1'],
		]);
	}

	$form_body .= '</div>';
}

$form_body .= '</div>';

$form_body .= elgg_format_element('span', ['id' => 'event-manager-search-form-past-events'], elgg_view_field([
	'#type' => 'checkboxes',
	'name' => 'past_events',
	'data-slide-toggle' => 0,
	'value' => 0,
	'options' => [elgg_echo('event_manager:list:includepastevents') => '1'],
]));

echo $form_body;
