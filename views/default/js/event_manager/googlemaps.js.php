<?php
?>
//<script>
var event_manager_gmap;
var event_manager_geocoder;
var event_manager_gmarkers = [];

$(function() {
			
	$(document).on('submit', '#event_manager_address_search', function(e) {
		searchAddress($('#address_search').val());
		e.preventDefault();
	});

	$(document).on('click', '#address_search_save', function() {
		var address = $('#address_search').val();
		
		$('#event_manager_event_edit input[name="location"]').val(address);
		if (address){
			event_manager_geocoder.geocode( { 'address': address}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					$('#event_latitude').val(results[0].geometry.location.lat());
					$('#event_longitude').val(results[0].geometry.location.lng());
				}
			});
		} else {
			$('#event_latitude').val("");
			$('#event_longitude').val("");
		}
		
		$.colorbox.close();
	});
});

function searchAddress(address) {
	if (event_manager_geocoder === null)	{
		event_manager_geocoder = new google.maps.Geocoder();
	}
	
	event_manager_geocoder.geocode( { 'address': address }, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			event_manager_gmap.setCenter(results[0].geometry.location);
			new google.maps.Marker({ map: event_manager_gmap, position: results[0].geometry.location });
			$('#address_search').val(results[0].formatted_address);
		}
	});
}

function setLatLngFields(point) {
   $('#event_latitude').val(point.lat());
   $('#event_longitude').val(point.lng());
}

function setAddressFields(address) {
   $('#address_search').val(address);
   $('#event_manager_event_edit input[name="location"]').val(address);
}