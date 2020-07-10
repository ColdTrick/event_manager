

//<script>
	/*
function initAutocomplete() {	    
	  autocomplete = new google.maps.places.Autocomplete(
      document.getElementById('address_search'), {types: ['geocode']});
}
	*/
	
	
	  function initMap() {
		  //alert('gmap');
		  					var la=($('#event_manager_event_edit input[name="latitude"]').val());
							var ln=($('#event_manager_event_edit input[name="longitude"]').val());
		
    


		  		
       var map = new google.maps.Map(document.getElementById('event-manager-maps-location-search'), {
          center: {lat: la, lng: ln},
          zoom: 13
        });
		  
     
        var input = document.getElementById('address_search');
        var autocomplete = new google.maps.places.Autocomplete(input);

        // Bind the map's bounds (viewport) property to the autocomplete object,
        // so that the autocomplete requests use the current map bounds for the
        // bounds option in the request.
        autocomplete.bindTo('bounds', map);

        // Set the data fields to return when the user selects a place.
        autocomplete.setFields(
            ['address_components', 'geometry', 'icon', 'name']);

        var infowindow = new google.maps.InfoWindow();
        var infowindowContent = document.getElementById('infowindow-content');
        infowindow.setContent(infowindowContent);
        var marker = new google.maps.Marker({
          map: map,
          anchorPoint: new google.maps.Point(0, -29)
        });

        autocomplete.addListener('place_changed', function() {
		
          infowindow.close();
          marker.setVisible(false);
          var place = autocomplete.getPlace();
          if (!place.geometry) {
            // User entered the name of a Place that was not suggested and
            // pressed the Enter key, or the Place Details request failed.
            window.alert("No details available for input: '" + place.name + "'");
            return;
          }

          // If the place has a geometry, then present it on a map.
          if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
          } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);  // Why 17? Because it looks good.
          }
          marker.setPosition(place.geometry.location);
          marker.setVisible(true);
			
			//display save button
		  document.getElementById('address_search_save').style.display = "block";

		  document.getElementsByName('latitude').value = place.geometry.location.lat();
		  document.getElementsByName('longitude').value = place.geometry.location.lng();




			var address = '';
          if (place.address_components) {
            address = [
              (place.address_components[0] && place.address_components[0].short_name || ''),
              (place.address_components[1] && place.address_components[1].short_name || ''),
              (place.address_components[2] && place.address_components[2].short_name || '')
            ].join(' ');
          }

        //  infowindowContent.children['place-icon'].src = place.icon;
        //  infowindowContent.children['place-name'].textContent = place.name;
        //  infowindowContent.children['place-address'].textContent = address;
        //  infowindow.open(map, marker);
        });

      
      }
"use strict";
	  define('gmaps', ['jquery', '//maps.googleapis.com/maps/api/js?key=<?php echo elgg_get_plugin_setting('google_api_key', 'event_manager'); ?>&libraries=places&callback=initMap']);
