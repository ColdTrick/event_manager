<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof Event) {
	return;
}

$lat = $entity->getLatitude();
$long = $entity->getLongitude();

if (empty($lat) || empty($long)) {
	return;
}

elgg_load_external_file('css', 'leafletjs');

echo elgg_format_element('div', [
	'id' => 'event-manager-leafletjs-map',
	'class' => 'event-manager-event-view-maps',
]);

$event_zoom_level = (int) elgg_get_plugin_setting('osm_detail_zoom', 'event_manager', 12);

?>
<script>
	require(['event_manager/osm'], function (EventMap) {
		EventMap.setup({
			element: 'event-manager-leafletjs-map',
			lat: <?= $lat ?>,
			lng: <?= $long ?>,
			zoom: <?= $event_zoom_level ?>,
		}).addMarker({
			lat: <?= $lat ?>,
			lng: <?= $long ?>
		});
	});
</script>
