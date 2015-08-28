<?php

$guid = get_input('guid');

echo '<div id="google_maps" style="width: 800px; height: 700px;">';
echo '<div id="map_canvas" style="width: 800px; height: 600px;"></div>';

$location = $event->location;
$form_body .= '<label>' . elgg_echo('event_manager:event:edit:maps_address') . '</label>';
$form_body .= elgg_view('input/text', [
	'name' => 'address_search',
	'id' => 'address_search',
	'value' => $location
]);

$form_body .= elgg_view('input/submit', [
	'class' => 'elgg-button-action',
	'name' => 'address_search_submit',
	'value' => elgg_echo('search')
]) . '&nbsp';

$form_body .= elgg_view('input/button', [
	'class' => 'elgg-button-submit',
	'name' => 'address_search_save',
	'id' => 'address_search_save',
	'value' => elgg_echo('save')
]);

echo elgg_view('input/form', [
	'id' => 'event_manager_address_search',
	'name' => 'event_manager_address_search',
	'body' => $form_body
]);

echo '</div>';
