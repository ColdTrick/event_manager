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

elgg_load_css('leafletjs');

echo elgg_format_element('div', [
	'id' => 'event-manager-leafletjs-map',
	'class' => 'event-manager-event-view-maps',
]);

?>
<script>
	require(['event_manager/osm'], function (EventMap) {
		EventMap.setup({
			element: 'event-manager-leafletjs-map',
			lat: <?= $lat ?>,
			lng: <?= $long ?>
		}).addMarker({
			lat: <?= $lat ?>,
			lng: <?= $long ?>
		});
	});
</script>
