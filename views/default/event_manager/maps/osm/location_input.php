<?php

elgg_require_js('event_manager/maps/osm/location_input');
elgg_load_css('leafletjs');

$field_options = (array) elgg_extract('field_options', $vars);
$field_options['readonly'] = true;

echo elgg_view_field($field_options);
