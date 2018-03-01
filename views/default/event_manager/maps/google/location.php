<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof Event) {
	return;
}

$location = $entity->location;
if (empty($location)) {
	return;
}

echo elgg_format_element('div', [
	'id' => 'event-manager-gmaps-location',
	'class' => 'event-manager-event-view-maps',
]);

$zoom_level = (int) elgg_get_plugin_setting('google_maps_detail_zoom', 'event_manager', 12);

?>
<script>
	require(['event_manager/maps'], function (EventMap) {
		EventMap.setup('#event-manager-gmaps-location', '<?php echo $location; ?>', {
			zoom: <?= $zoom_level ?>
		});
	});
</script>
