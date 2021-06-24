define(['jquery', 'elgg', 'elgg/Ajax', 'elgg/lightbox', 'event_manager/maps'], function($, elgg, Ajax, lightbox, EventMap) {
	
	var execute_search_map = function(event) {
			
		var mapBounds = elgg.event_manager.map.gmap.getBounds();
		var latitude = mapBounds.getCenter().lat();
		var longitude = mapBounds.getCenter().lng();
		var distance_latitude = mapBounds.getNorthEast().lat() - latitude;
		var distance_longitude = mapBounds.getNorthEast().lng() - longitude;
		if (distance_longitude < 0) {
			distance_longitude = 360 + distance_longitude;
		}
				
		var ajax = new Ajax();
		
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
				
				var shadowIcon = new google.maps.MarkerImage("//chart.apis.google.com/chart?chst=d_map_pin_shadow",
					new google.maps.Size(40, 37),
					new google.maps.Point(0, 0),
					new google.maps.Point(12, 35));
	
				$.each(data.markers, function(i, event) {
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
					};
					
					if (event.iscreator) {
						markerOptions.icon = "//maps.google.com/mapfiles/ms/icons/yellow-dot.png";
					} else {
						if (event.has_relation) {
							markerOptions.icon = "//maps.google.com/mapfiles/ms/icons/blue-dot.png";
						}
					}
					
					elgg.event_manager.markers[event.guid] = elgg.event_manager.map.gmap.addMarker(markerOptions).addListener('click', function() {
						lightbox.open({
							'href': elgg.normalize_url('ajax/view/event_manager/event/popup?guid=' + event.guid),
						});
					});
				});
			}
		});
	};
	
	var initialize_tab = function() {
		if (typeof elgg.event_manager.map === 'undefined') {
			elgg.event_manager.map = EventMap.setup('#event_manager_onthemap_canvas');
			elgg.event_manager.map.gmap.addListener('idle', execute_search_map);
		} else {
			execute_search_map();
		}
	};
	
	elgg.register_hook_handler('tab:onthemap', 'event_manager', initialize_tab);
	elgg.register_hook_handler('search:onthemap', 'event_manager', execute_search_map);
});
