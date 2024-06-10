import 'jquery';
import lightbox from 'elgg/lightbox';
import EventMap from 'event_manager/maps/osm/osm';

var event_map;
var location_data;

var createAddress = function(addressData) {
	
	var address = addressData.road;
	if (addressData.house_number) {
		address += ' ' + addressData.house_number;
	}
	
	if (addressData.city) {
		address += ', ' + addressData.city;
	} else if (addressData.town) {
		address += ', ' + addressData.town;
	} else if (addressData.suburb) {
		address += ', ' + addressData.suburb;
	}
	
	address += ', ' + addressData.country;
	
	return address;
};

var executeSearch = function() {
	var $search_form = $('#event-manager-edit-maps-search-container');
	
	event_map.clearMarkers();
	
	event_map.getGeocode($search_form.find('input[name="address_search"]').val(), function(result) {
		location_data = result;
		
		$('#event-manager-edit-maps-search-container input[name="address_search"]').val(createAddress(location_data.address));
		
		$search_form.find('[name="address_search_save"]').removeClass('hidden');
		
		event_map.moveToLatLng(location_data.lat, location_data.lon, true);
	});
};

$('#event_manager_event_edit input[name="location"]').on('focus', function() {
	var $elem = $(this);
	
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
			
			import('leafletjs').then((leaflet) => {
				if (!event_map) {
					var lat = $('#event_manager_event_edit input[name="latitude"]').val();
					var lng = $('#event_manager_event_edit input[name="longitude"]').val();
					
					event_map = EventMap.setup({
						element: 'event-manager-maps-location-search'
					});
					if (lat && lng) {
						event_map.moveToLatLng(lat, lng, true);
					} else {
						event_map.moveToDefaultLocation();
					}
				}
			});
		}
	});
});

$(document).on('keyup', '#event-manager-edit-maps-search-container input[name="address_search"]', function(event) {
	if (event.keyCode == 13) {
		executeSearch();
	} else {
		$('#event-manager-edit-maps-search-container [name="address_search_save"]').addClass('hidden');
	}
});
$(document).on('click', '#event-manager-edit-maps-search-container button[name="address_search_submit"]', function() {
	executeSearch();
});

$(document).on('click', '#event-manager-edit-maps-search-container button[name="address_search_save"]', function() {
	var address = $('#event-manager-edit-maps-search-container input[name="address_search"]').val();
	var $location_field = $('#event_manager_event_edit input[name="location"]');
	var $latitude = $('#event_manager_event_edit input[name="latitude"]');
	var $longitude = $('#event_manager_event_edit input[name="longitude"]');

	if (location_data) {
		$location_field.val(createAddress(location_data.address));

		$latitude.val(location_data.lat);
		$longitude.val(location_data.lon);
		
		$('#event-manager-location-input-delete').closest('.elgg-field').removeClass('hidden');
	} else {
		$location_field.val('');
		$latitude.val('');
		$longitude.val('');
	}
	
	lightbox.close();
});
