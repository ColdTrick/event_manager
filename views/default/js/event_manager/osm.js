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
		
		// add marker group
		this.markerGroup = leaflet.layerGroup().addTo(this.event_map);
	};
		
	EventMap.prototype = {
		moveToLatLng : function(lat, lng, add_marker) {
			this.event_map.setView([lat, lng], elgg.data.event_manager_osm_default_zoom);

			if (add_marker == true) {
				this.addMarker([lat, lng]);
			}
			
		},
		moveToDefaultLocation : function() {
			this.event_map.setView([elgg.data.event_manager_osm_default_location_lat, elgg.data.event_manager_osm_default_location_lng], elgg.data.event_manager_osm_default_zoom);			
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
			return leaflet.marker(options).addTo(this.markerGroup);
		},
		clearMarkers : function() {
			this.markerGroup.clearLayers();
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
