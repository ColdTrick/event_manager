<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof Event) {
	return;
}

elgg_require_js('event_manager/maps/google/location_input');

$field_options = (array) elgg_extract('field_options', $vars);
$field_options['readonly'] = true;

echo elgg_view_field($field_options);

$form = '<div id="event-manager-gmaps-location-search"></div>';

$form .= elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('event_manager:event:edit:maps_address'),
	'name' => 'address_search',
	'id' => 'address_search',
]);

$form .= elgg_view_field([
	'#type' => 'fieldset',
	'align' => 'horizontal',
	'fields' => [
		[
			'#type' => 'button',
			'class' => 'elgg-button-submit elgg-button-action',
			'name' => 'address_search_submit',
			'value' => elgg_echo('search'),
		],
		[
			'#type' => 'button',
			'class' => 'elgg-button-submit hidden',
			'name' => 'address_search_save',
			'id' => 'address_search_save',
			'value' => elgg_echo('save'),
		],
	],
]);

$container = elgg_format_element('div', [
	'id' => 'event-manager-edit-maps-search-container',
], $form);

echo elgg_format_element('div', ['class' => 'hidden'], $container);
