<?php

elgg_import_esm('event_manager/maps/osm/location_input');
elgg_load_external_file('css', 'leafletjs');

$field_options = (array) elgg_extract('field_options', $vars);

echo elgg_view_field($field_options);
