define(['jquery', 'elgg'], function($, elgg) {
	
	var execute_search_map = function(event) {
		
		require(['elgg/spinner'], function(spinner) {
			spinner.start();
			
			var mapBounds = elgg.event_manager.map.gmap.getBounds();
			var latitude = mapBounds.getCenter().lat();
			var longitude = mapBounds.getCenter().lng();
			var distance_latitude = mapBounds.getNorthEast().lat() - latitude;
			var distance_longitude = mapBounds.getNorthEast().lng() - longitude;
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
					
					elgg.event_manager.infowindow = new google.maps.InfoWindow();

					var shadowIcon = new google.maps.MarkerImage("//chart.apis.google.com/chart?chst=d_map_pin_shadow",
						new google.maps.Size(40, 37),
						new google.maps.Point(0, 0),
						new google.maps.Point(12, 35));
					var ownIcon = "//maps.google.com/mapfiles/ms/icons/yellow-dot.png";
					var attendingIcon = "//maps.google.com/mapfiles/ms/icons/blue-dot.png";

					$.each(response.markers, function(i, event) {
						if (!elgg.event_manager.markers) {
							elgg.event_manager.markers = [];
						}
						
						if (elgg.event_manager.markers[event.guid]) {
							// already added, so return
							return;
						}

						var markerOptions = {
							lat: event.lat, 
							lng: event.lng,
							animation: google.maps.Animation.DROP,
							title: event.title,
							shadow: shadowIcon,
							infoWindow: {
								content: event.html
							},
						};
						
						if (event.iscreator) {
							markerOptions.icon = ownIcon;
						} else {
							if (event.has_relation) {
								markerOptions.icon = attendingIcon;
							}
						}
						
						elgg.event_manager.markers[event.guid] = elgg.event_manager.map.gmap.addMarker(markerOptions);
					});
				},
				complete: function() {
					spinner.stop();
				}
			});
		});
	};
	
	var initialize_tab = function() {
		if (typeof elgg.event_manager.map === 'undefined') {
			require(['elgg/spinner'], function(spinner) {
				spinner.start();
				
				require(['event_manager/maps'], function (EventMap) {
					elgg.event_manager.map = EventMap.setup('#event_manager_onthemap_canvas');
					elgg.event_manager.map.gmap.addListener('idle', execute_search_map);
				});
			});
		} else {
			execute_search_map();
		}
	};
	
	elgg.register_hook_handler('tab:onthemap', 'event_manager', initialize_tab);
	elgg.register_hook_handler('search:onthemap', 'event_manager', execute_search_map);
});
