define(['jquery'], function($) {
	
	var executeSearch = function() {
		var $search_form = $('#event-manager-edit-maps-search-container');
		elgg.event_manager.map.setLocation($search_form.find('input[name="address_search"]').val());
		$search_form.find('[name="address_search_save"]').removeClass('hidden');
	};
	
	$('#event_manager_event_edit input[name="location"]').on('focus', function() {
		var $elem = $(this);

		require(['elgg/lightbox'], function(lightbox) {
			lightbox.open({
				'inline': true,
				'href': '#event-manager-edit-maps-search-container',
				'onComplete': function () {

					var current_location = $elem.val();
					var $container = $('#event-manager-edit-maps-search-container');
					if (current_location) {
						$container.find('input[name="address_search"]').val(current_location);
						$container.find('[name="address_search_save"]').removeClass('hidden');
					}
					
					$container.find('input[name="address_search"]').focus();
					
					require(['event_manager/maps'], function (EventMap) {
						if (!elgg.event_manager.map) {
							elgg.event_manager.map = EventMap.setup('#event-manager-maps-location-search');
							elgg.event_manager.map.setLocation(current_location);
						}
					});
				}
			});
		});
	});
	
	$(document).on('keyup', '#event-manager-edit-maps-search-container input[name="address_search"]', function(event) {
		if (event.keyCode == 13) {
			executeSearch();
		} else {
			$('#event-manager-edit-maps-search-container [name="address_search_save"]').addClass('hidden');
		}
	});
	$(document).on('click', '#event-manager-edit-maps-search-container input[name="address_search_submit"]', function() {
		executeSearch();
	});
	
	$(document).on('click', '#event-manager-edit-maps-search-container input[name="address_search_save"]', function() {
		
		var address = $('#event-manager-edit-maps-search-container input[name="address_search"]').val();
		var $location_field = $('#event_manager_event_edit input[name="location"]');
		var $latitude = $('#event_manager_event_edit input[name="latitude"]');
		var $longitude = $('#event_manager_event_edit input[name="longitude"]');

		if (address) {
			elgg.event_manager.map.getGeocode(address, function(results, status) {
				if (status == 'OK') {
					$location_field.val(results[0].formatted_address);

					$latitude.val(results[0].geometry.location.lat());
					$longitude.val(results[0].geometry.location.lng());
				}
			});
			
			$('#event-manager-location-input-delete').closest('.elgg-field').removeClass('hidden');
		} else {
			$location_field.val('');
			$latitude.val('');
			$longitude.val('');
		}
		
		$.colorbox.close();
	});
});
