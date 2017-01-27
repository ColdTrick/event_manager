<?php

echo '<div id="event-manager-gmaps-location-search"></div>';

$form_body = elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('event_manager:event:edit:maps_address'),
	'name' => 'address_search',
	'id' => 'address_search',
]);

$form_body .= elgg_view('input/button', [
	'class' => 'elgg-button-submit elgg-button-action mrm',
	'name' => 'address_search_submit',
	'value' => elgg_echo('search'),
]);

$form_body .= elgg_view('input/button', [
	'class' => 'elgg-button-submit hidden',
	'name' => 'address_search_save',
	'id' => 'address_search_save',
	'value' => elgg_echo('save'),
]);

echo $form_body;
