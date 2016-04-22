<?php
$event = elgg_extract('entity', $vars);

echo elgg_format_element('div', [
	'id' => 'event-manager-gmaps-location',
	'class' => 'event-manager-gmaps-location',
	'data-gmaps-options' => [
		'location' => $event->location
	],
]);

?>
<script>
	require(['gmaps'], function (GMaps) {
		var map = new GMaps({
			div: '#event-manager-gmaps-location',
			lat: -12.043333,
			lng: -77.028333,
			mapTypeControl: true,
		    zoomControl: true,
		    streetViewControl: true,
		    fullscreenControl: true
		});

		GMaps.geocode({
			address: '<?php echo $event->location; ?>',
			callback: function(results, status) {
				if (status == 'OK') {
					var latlng = results[0].geometry.location;
					map.setCenter(latlng.lat(), latlng.lng());
					map.addMarker({
						lat: latlng.lat(),
						lng: latlng.lng()
					});
				}
			}
		});
	});
</script>