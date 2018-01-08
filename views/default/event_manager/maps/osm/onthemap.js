define(['jquery', 'elgg'], function($, elgg) {
	
	var event_map;
	var current_markers = [];
	
	var execute_search_map = function(event) {
		if (!event_map) {
			return;
		}
		
		require(['elgg/spinner'], function(spinner) {
			spinner.start();
			
			var mapBounds = event_map.getMap().getBounds();
			var latitude = mapBounds.getCenter().lat;
			var longitude = mapBounds.getCenter().lng;
			var distance_latitude = mapBounds.getNorthEast().lat - latitude;
			var distance_longitude = mapBounds.getNorthEast().lng - longitude;
			if (distance_longitude < 0) {
				distance_longitude = 360 + distance_longitude;
			}
		
			$("#latitude").val(latitude);
			$("#longitude").val(longitude);
			$("#distance_latitude").val(distance_latitude);
			$("#distance_longitude").val(distance_longitude);
			
			elgg.action('event_manager/event/search', {
				data: $('#event_manager_search_form').serialize(),
				success: function(data) {
					var response = data.output;

					if (!response.markers) {
						return;
					}
					
//					elgg.event_manager.infowindow = new google.maps.InfoWindow();
//
//					var shadowIcon = new google.maps.MarkerImage("//chart.apis.google.com/chart?chst=d_map_pin_shadow",
//						new google.maps.Size(40, 37),
//						new google.maps.Point(0, 0),
//						new google.maps.Point(12, 35));
//					var ownIcon = "//maps.google.com/mapfiles/ms/icons/yellow-dot.png";
//					var attendingIcon = "//maps.google.com/mapfiles/ms/icons/blue-dot.png";

					$.each(response.markers, function(i, event) {
						if (current_markers[event.guid]) {
							// already added, so return
							return;
						}

						var markerOptions = {
							lat: event.lat, 
							lng: event.lng,
						};
						
//						if (event.iscreator) {
//							markerOptions.icon = ownIcon;
//						} else {
//							if (event.has_relation) {
//								markerOptions.icon = attendingIcon;
//							}
//						}
						
						event_map.addMarker(markerOptions).bindPopup(event.html);
						
						current_markers[event.guid] = true;
					});
				},
				complete: function() {
					spinner.stop();
				}
			});
		});
	};
	
	var initialize_tab = function() {
		if (typeof event_map === 'undefined') {
			require(['event_manager/osm'], function (EventMap) {
				event_map = EventMap.setup({
					element: 'event_manager_onthemap_canvas',
					lat: 12,
					lng: 12
				});
				
				event_map.getMap().on('moveend', execute_search_map);
				execute_search_map();
			});
		} else {
			execute_search_map();
		}
	};
	
	elgg.register_hook_handler('tab:onthemap', 'event_manager', initialize_tab);
	elgg.register_hook_handler('search:onthemap', 'event_manager', execute_search_map);
});
