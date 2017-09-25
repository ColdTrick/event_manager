<?php

$location = elgg_get_plugin_setting('google_maps_default_location', 'event_manager');
if (empty($location)) {
	$location = 'Netherlands';
}

$zoom_level = (int) elgg_get_plugin_setting('google_maps_default_zoom', 'event_manager', 10);

?>
//<script>
define(['jquery', 'elgg', 'gmaps'], function($, elgg, GMaps) {
	function EventMap(options) {
		var map_attrs = {
			mapTypeControl: true,
			mapType: 'roadmap',
			zoomControl: true,
			zoom: <?php echo $zoom_level; ?>,
			streetViewControl: true,
			fullscreenControl: true,
			lat: 0,
			lng: 0,
		};

		if (options) {
			for (var attrname in options) {
				map_attrs[attrname] = options[attrname];
			}
		}
		
		this.gmap = new GMaps(map_attrs);
	};
		
	EventMap.prototype = {
		setLocation : function(location, add_marker) {
			if (add_marker !== false) {
				add_marker = true;
			}
			
			var gmap = this.gmap;
			GMaps.geocode({
				address: location,
				callback: function(results, status) {
					if (status == 'OK') {
						var latlng = results[0].geometry.location;
						gmap.setCenter(latlng.lat(), latlng.lng());

						if (add_marker) {
							gmap.addMarker({
								lat: latlng.lat(),
								lng: latlng.lng()
							});
						}
					}
				}
			});
		},
		setDefaultLocation : function() {
			var gmap = this.gmap;
			GMaps.geocode({
				address: '<?php echo $location; ?>',
				callback: function(results, status) {
					if (status == 'OK') {
						var latlng = results[0].geometry.location;
						gmap.setCenter(latlng.lat(), latlng.lng());
					}
				}
			});
		},
		getGeocode : function(address, callback) {
			GMaps.geocode({
				address: address,
				callback: callback
			});
		},
	};

	EventMap.setup = function(element, address, options) {
		if (!options) {
			options = {};
		}
		options['div'] = element;
		var map = new EventMap(options);

		if (address) {
			map.setLocation(address);
		} else {
			map.setDefaultLocation();
		}

		return map;
	};
	
	return EventMap;
});