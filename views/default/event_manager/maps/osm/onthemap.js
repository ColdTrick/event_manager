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
		
		ajax.action('event_manager/maps/data', {
			data: {
				latitude: latitude,
				longitude: longitude,
				distance_latitude: distance_latitude,
				distance_longitude: distance_longitude,
			},
			success: function(data) {
				console.log(data);
				
				if (!data.markers) {
					return;
				}
				
				$.each(data.markers, function(i, event) {
					if (current_markers[event.guid]) {
						// already added, so return
						return;
					}

					var markerOptions = {
						lat: event.lat, 
						lng: event.lng,
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
