define(['jquery', 'elgg', 'elgg/Ajax'], function($, elgg, Ajax) {
	var event_map;
	var current_markers = [];
	
	var execute_search_map = function(event) {
		if (!event_map) {
			return;
		}
		
		var ajax = new Ajax();
		
		var mapBounds = event_map.getMap().getBounds();
		var latitude = mapBounds.getCenter().lat;
		var longitude = mapBounds.getCenter().lng;
		var distance_latitude = mapBounds.getNorthEast().lat - latitude;
		var distance_longitude = mapBounds.getNorthEast().lng - longitude;
		if (distance_longitude < 0) {
			distance_longitude = 360 + distance_longitude;
		}
		
		var canvas_data = $('#event_manager_onthemap_canvas').data();
		
		ajax.action('event_manager/maps/data', {
			data: {
				...canvas_data,
				latitude: latitude,
				longitude: longitude,
				distance_latitude: distance_latitude,
				distance_longitude: distance_longitude
			},
			success: function(data) {
				if (!data.markers) {
					return;
				}
				
				$.each(data.markers, function(i, event) {
					if (current_markers[event.guid]) {
						// already added, so return
						return;
					}
					
					var custom_icon_options = {
						iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
						shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
						iconSize: [25, 41],
						iconAnchor: [12, 41],
						popupAnchor: [1, -34],
						shadowSize: [41, 41],
						...elgg.event_manager.maps_osm_icon_default
					}
					
					if (event.iscreator) {
						custom_icon_options = {
							...custom_icon_options,
							...{
								iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-yellow.png'
							},
							...elgg.event_manager.maps_osm_icon_owned
						}
					} else {
						if (event.has_relation) {
							custom_icon_options = {
								...custom_icon_options,
								...{
									iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png'
								},
								...elgg.event_manager.maps_osm_icon_attending
							}
						}
					}

					var markerOptions = {
						lat: event.lat, 
						lng: event.lng,
						icon: custom_icon_options
					};
										
					event_map.addMarker(markerOptions).bindPopup(event.html);
					
					current_markers[event.guid] = true;
				});
			}
		});
	};
	
	var initialize_tab = function() {
		if (typeof event_map === 'undefined') {
			require(['event_manager/osm'], function (EventMap) {
				event_map = EventMap.setup({
					element: 'event_manager_onthemap_canvas'
				});
				event_map.moveToDefaultLocation();
				event_map.getMap().on('moveend', execute_search_map);
				execute_search_map();
			});
		} else {
			execute_search_map();
		}
	};
	
	initialize_tab();
	
	elgg.register_hook_handler('search:onthemap', 'event_manager', execute_search_map);
});
