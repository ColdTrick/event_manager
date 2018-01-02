<?php

$zoom_level = (int) elgg_get_plugin_setting('osm_default_zoom', 'event_manager', 10);

?>
//<script>
define(['jquery', 'elgg', 'leafletjs'], function($, elgg, leaflet) {
	function EventMap(options) {
		this.event_map = leaflet.map(options.element);

		// create the tile layer with correct attribution
		var osmUrl = '//{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
		var osmAttrib = 'Map data © <a href="http://openstreetmap.org">OpenStreetMap</a> contributors';
				
		var osm = new leaflet.TileLayer(osmUrl, {
			attribution: osmAttrib
		});

		this.event_map.addLayer(osm);
	};
		
	EventMap.prototype = {
		moveToLatLng : function(lat, lng, add_marker) {
			this.event_map.setView([lat, lng], <?= $zoom_level ?>);

			if (add_marker == true) {
				this.addMarker([lat, lng]);
			}
			
		},
		getGeocode : function(address, callback) {
			var result = elgg.getJSON('http://nominatim.openstreetmap.org/search?q=' + address + '&format=json&limit=1&addressdetails=1', {
				success: function(data) {
					callback(data[0]);
				}
			});
			this.event_map.attributionControl.addAttribution('© <a href="http://nominatim.openstreetmap.org">Nominatim</a>');
		},
		addMarker : function(options) {
			return leaflet.marker(options).addTo(this.event_map);
		},
		getMap: function() {
			return this.event_map;
		}
		
	};

	EventMap.setup = function(options) {
		if (!options) {
			options = {};
		}

		if (!options.element) {
			console.log('Missing element to initialize map');
			return false;
		}
		
		var map = new EventMap(options);

		if (options.lat && options.lng) {
			map.moveToLatLng(options.lat, options.lng);
		}

		return map;
	};
	
	return EventMap;
});
