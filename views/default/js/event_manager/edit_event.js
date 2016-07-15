elgg.provide('elgg.event_manager');

elgg.event_manager.edit_event_init = function() {
	
	$('#event_manager_event_edit input[name="location"]').on('click', function(event, elem) {
		
		var $elem = $(this);
		var current_location = $elem.val();
		
		$('#event-manager-edit-maps-search-container input[name="address_search"]').val(current_location);
		
		require(['event_manager/maps'], function (EventMap) {
			if (!elgg.event_manager.map) {
				elgg.event_manager.map = EventMap.setup('#event-manager-gmaps-location-search');
				elgg.event_manager.map.setLocation(current_location);
			}
		});
	});
	
	$(document).on('keyup', '#event-manager-edit-maps-search-container input[name="address_search"]', function(event) {
		if (event.keyCode == 13) {
			elgg.event_manager.map.setLocation($('#event-manager-edit-maps-search-container input[name="address_search"]').val());
		}
	});
	$(document).on('click', '#event-manager-edit-maps-search-container input[name="address_search_submit"]', function() {
		elgg.event_manager.map.setLocation($('#event-manager-edit-maps-search-container input[name="address_search"]').val());
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
		} else {
			$location_field.val('');
			$latitude.val('');
			$longitude.val('');
		}
		
		$.colorbox.close();
	});
	
	$(document).on('change', 'input[name="fee"], input[name="max_attendees"]', function() {
		$toggle_field = $(this).parent().parent().next().find('.elgg-field'); 
		$toggle_field.addClass('hidden');
		var entered_value = $(this).val().trim();
		if (entered_value && (entered_value !== '0')) {
			$toggle_field.removeClass('hidden');
		}
	});
};

elgg.register_hook_handler('init', 'system', elgg.event_manager.edit_event_init);