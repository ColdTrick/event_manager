<?php
?>
<script type="text/javascript">

var event_manager_gmap;
var event_manager_geocoder;
var event_manager_gmarkers = [];
var markerTimeout;


$(function()
{
	var fancyboxOptions = {
			'onComplete': function()
			{
				var location = $('#event_manager_event_edit input[name="location"]').val();
				initMaps('map_canvas');
				
				if(location != '')
				{
					$('#address_search').val(location);
					event_manager_gmap.setCenter(new GLatLng($("#event_latitude").val(), $("#event_longitude").val()), 12);
					addMarker(new GLatLng($("#event_latitude").val(), $("#event_longitude").val()), true);
				}
				else
				{
					<?php
					$location = get_plugin_setting('google_maps_default_location', 'event_manager');
					$zoom_level = get_plugin_setting('google_maps_default_zoom', 'event_manager');
					?>
					moveMapToLocation('<?php echo $location;?>', <?php echo $zoom_level;?>); 
				}
			}
		};
	

	$('.openRouteToEvent').live('click', function(e)
	{
		clckElmnt = $(this); 

		$.fancybox({
			'href':clckElmnt.attr('href'),
			'onComplete': function()
			{
				initMaps('map_canvas');
				initRoutemaps(clckElmnt.html());
				
			}});
		e.preventDefault();
	});

	
	$('#openmaps').click(function()
	{
		$("#openGoogleMaps").click();
	});
	
	$("#openGoogleMaps").fancybox(fancyboxOptions);
});


/*
 * Global GoogleMaps init
 */
function initMaps(element)
{
	if(GBrowserIsCompatible())
	{
		event_manager_gmap = new GMap2(document.getElementById(element));
		event_manager_geocoder = new GClientGeocoder();
		
		event_manager_gmap.setMapType(G_NORMAL_MAP);
		event_manager_gmap.enableScrollWheelZoom();
		event_manager_gmap.setUIToDefault();
	}
}

function initRoutemaps(location)
{
	event_manager_geocoder.getLatLng(location, function(gpoint)
	{
		if(gpoint)
		{
			event_manager_gmap.setCenter(gpoint, 15);
			var gicon = new GIcon(G_DEFAULT_ICON);
				gicon.iconSize = new GSize(32,32);
				gicon.shadow = 'http://www.google.com/intl/en_us/mapfiles/ms/micons/pushpin_shadow.png';
				gicon.image = 'http://www.google.com/intl/en_us/mapfiles/ms/micons/red-pushpin.png';
				gicon.shadowSize = new GSize(59, 32);
			
			gmarker = new GMarker(gpoint, {icon: gicon});
			event_manager_gmap.addOverlay(gmarker);
		}
	});
}

function initOnthemaps()
{
	<?php
	$location = get_plugin_setting('google_maps_default_location', 'event_manager');
	$zoom_level = get_plugin_setting('google_maps_default_zoom', 'event_manager');
	
	if($location == '')
	{
		$location = 'Netherlands';
	}
	
	if(!$zoom_level)
	{
		$zoom_level = 7;
	}
	?>
	moveMapToLocation('<?php echo $location;?>', <?php echo $zoom_level;?>);
	
	GEvent.addListener(event_manager_gmap, "dragend", function() 
	{
		getMarkersJson();
	});
	GEvent.addListener(event_manager_gmap, "movestart", function() 
	{
		getMarkersJson();		
	});
	GEvent.addListener(event_manager_gmap, "moveend", function() 
	{
		getMarkersJson();		
	});
	GEvent.addListener(event_manager_gmap, "dblclick", function() 
	{
		getMarkersJson();	
	});
	GEvent.addListener(event_manager_gmap, "zoomend", function() 
	{
		getMarkersJson();	
	});
}

function addMarkers(lat, lng, title, text, hasrelation, iscreator)
{
	var gpoint = new GLatLng(lat, lng), gicon = new GIcon(G_DEFAULT_ICON), gimage = '';

	gicon.iconSize = new GSize(32,32);
	gicon.shadow = 'http://www.google.com/intl/en_us/mapfiles/ms/micons/pushpin_shadow.png';
	gicon.shadowSize = new GSize(59, 32);

	gimage = 'http://www.google.com/intl/en_us/mapfiles/ms/micons/red-pushpin.png';	
	cat = 'normal';
	
	if(iscreator != null)
	{
		gimage = 'http://www.google.com/intl/en_us/mapfiles/ms/micons/ylw-pushpin.png';
		cat = 'creator';
	}
	else
	{
		if(hasrelation != null)
		{
			gimage = 'http://www.google.com/intl/en_us/mapfiles/ms/micons/blue-pushpin.png';
			cat = 'attending';
					
		}
	}

	gicon.image = gimage;
	
	gmarker = new GMarker(gpoint, {icon: gicon});
	gmarker.myname = title;
	gmarker.mycategory = cat;

	GEvent.addListener(gmarker, "click", function() 
	{
		event_manager_gmap.openInfoWindowHtml(gpoint, text);
	});
	
	return gmarker;
}

function makeSidebar() 
{
	var html = '<div class="contentWrapper" id="event_manager_onthemap_sidebar"><ul>', foundMarkers = 0;
	$.each(event_manager_gmarkers, function(i, event)
	{
		if (!event.isHidden())
		{
			foundMarkers = (parseInt(foundMarkers, 10)+1);
			html += '<li><a href="javascript:openInfowindow(' + i + ');">' + event.myname + '<\/a><\/li>';
		}
	});
	html += '</ul></div>';

	if(foundMarkers > 0)
	{
		$('#owner_block_bottom').html(html);
	}
	else
	{
		$('#owner_block_bottom').html('');
	}
}

function getMarkersJson()
{ 
	var bounds = event_manager_gmap.getBounds(), southWest = bounds.getSouthWest(), northEast = bounds.getNorthEast();

	$.each(event_manager_gmarkers, function(i, event)
	{
		var lat = event.getLatLng().lat();
		var lng = event.getLatLng().lng();
		
		if(lat <= northEast.lat() && lng >= southWest.lng() && lat >= southWest.lat() && lng <= northEast.lng())
		{
			if(event.isHidden())
			{
				event.show();
			}
		}
		else
		{
			event.closeInfoWindow(); 
			event.hide();
		}
	});
	makeSidebar();
}

function getLatLngFromFields()
{
	var latField = $("#event_latitude").val(), lngField = $("#event_longitude").val();
	if(latField != '' && lngField != '')
	{
		return new GLatLng($("#event_latitude").val(), $("#event_longitude").val());
	}
	else
	{
		return null;
	}
}

function getCoordsFromAddress(address)
{
	var coords = false;
	if (event_manager_geocoder == null)
	{
		event_manager_geocoder = new GClientGeocoder();
	}
	event_manager_geocoder.getLatLng(address, function(gpoint)
	{
		if(gpoint)
		{
			setLatLngFields(gpoint);
			getAdressFromCoords(gpoint);
			addMarker(gpoint, true);
		}
	});
	
	return coords;
}

function searchAddress(address)
{
	if (event_manager_geocoder == null)
	{
		event_manager_geocoder = new GClientGeocoder();
	}
	event_manager_geocoder.getLatLng(address, function(gpoint)
	{
		if(gpoint)
		{
			event_manager_geocoder.getLocations(gpoint, function(response)
			{
				if(response)
				{
					result = response.Placemark[0].address;
					$('#address_search').val(result);
					addMarker(gpoint, true);
				}
			});	
		}
	});
}

function setLatLngFields(point)
{
   $('#event_latitude').val(point.lat());
   $('#event_longitude').val(point.lng());
}

function getAdressFromCoords(coords, fields)
{
	var address = null;
	if(coords == null)
	{
		coords = event_manager_gmap.getCenter();
	}
	if (event_manager_geocoder == null)
	{
		event_manager_geocoder = new GClientGeocoder();
	}
	event_manager_geocoder.getLocations(coords, function(response)
	{
		if(response)
		{
			address = response.Placemark[0].address;
			setAddressFields(address);
		}
	});	
}

function addMarker(gpoint, draggable)
{
	event_manager_gmap.clearOverlays();
	
	var gicon = new GIcon(G_DEFAULT_ICON);
		gicon.iconSize = new GSize(32,32);
		gicon.shadow = 'http://www.google.com/intl/en_us/mapfiles/ms/micons/pushpin_shadow.png';
		gicon.image = 'http://www.google.com/intl/en_us/mapfiles/ms/micons/red-pushpin.png';
		gicon.shadowSize = new GSize(59, 32);
	
	gmarker = new GMarker(gpoint, {icon: gicon, draggable: draggable});
	event_manager_gmap.addOverlay(gmarker);
	
	GEvent.addListener(gmarker, "dragend", function() 
	{
		setLatLngFields(gmarker.getPoint());
		moveMapToCoords(gmarker.getPoint());
		getAdressFromCoords(gmarker.getPoint());
	});
	moveMapToCoords(gpoint);
	return gmarker;
}


function moveMapToCoords(point)
{
	event_manager_gmap.panTo(point);
}

function setAddressFields(address)
{
   $('#address_search').val(address);
   $('#event_manager_event_edit input[name="location"]').val(address);
}

function openInfowindow(i)
{
	GEvent.trigger(event_manager_gmarkers[i],"click");
}

function moveMapToLocation(location, zoomlevel)
{
	if (event_manager_geocoder == null)
	{
		event_manager_geocoder = new GClientGeocoder();
	}
	
	event_manager_geocoder.getLatLng(location, function(gpoint)
	{
		if(gpoint)
		{
			event_manager_gmap.setCenter(gpoint, zoomlevel);
		}
		/*else
		{
			event_manager_geocoder.getLatLng('atlantic ocean', function(gpoint)
			{
				event_manager_gmap.setCenter(gpoint, 2);
			});
		}*/
	});
}
</script>