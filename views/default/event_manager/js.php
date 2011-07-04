<?php
?>
<!-- Event manager -->
(function(){

    var uid1 = 'D' + (+new Date()),
        uid2 = 'D' + (+new Date() + 1);

    jQuery.event.special.focus = {
        setup: function() {
            var _self = this,
                handler = function(e) {
                    e = jQuery.event.fix(e);
                    e.type = 'focus';
                    if (_self === document) {
                        jQuery.event.handle.call(_self, e);
                    }
                };

            jQuery(this).data(uid1, handler);

            if (_self === document) {
                /* Must be live() */
                if (_self.addEventListener) {
                    _self.addEventListener('focus', handler, true);
                } else {
                    _self.attachEvent('onfocusin', handler);
                }
            } else {
                return false;
            }

        },
        teardown: function() {
            var handler = jQuery(this).data(uid1);
            if (this === document) {
                if (this.removeEventListener) {
                    this.removeEventListener('focus', handler, true);
                } else {
                    this.detachEvent('onfocusin', handler);
                }
            }
        }
    };

    jQuery.event.special.blur = {
        setup: function() {
            var _self = this,
                handler = function(e) {
                    e = jQuery.event.fix(e);
                    e.type = 'blur';
                    if (_self === document) {
                        jQuery.event.handle.call(_self, e);
                    }
                };

            jQuery(this).data(uid2, handler);

            if (_self === document) {
                /* Must be live() */
                if (_self.addEventListener) {
                    _self.addEventListener('blur', handler, true);
                } else {
                    _self.attachEvent('onfocusout', handler);
                }
            } else {
                return false;
            }

        },
        teardown: function() {
            var handler = jQuery(this).data(uid2);
            if (this === document) {
                if (this.removeEventListener) {
                    this.removeEventListener('blur', handler, true);
                } else {
                    this.detachEvent('onfocusout', handler);
                }
            }
        }
    };

}());

/* Google Maps Integration for Event Manager */

var event_manager_gmap;
var event_manager_geocoder;

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

function moveMapToCountry(country)
{
	if (event_manager_geocoder == null)
	{
		event_manager_geocoder = new GClientGeocoder();
	}
	event_manager_geocoder.getLatLng(country, function(gpoint)
	{
		event_manager_gmap.setCenter(gpoint, 7);
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
			moveMapToCoords(gpoint);
			getAdressFromCoords(gpoint);
			addMarker(gpoint, true);
		}
	});
	
	return coords;
}

function getAdressFromCoords(coords)
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

function setAddressFields(address)
{
   $('#address_search').val(address);
   $('#event_manager_event_edit input[name="location"]').val(address);
}

function setLatLngFields(point)
{
   $('#event_latitude').val(point.lat());
   $('#event_longitude').val(point.lng());
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

/*
 * Event listing: Onthemaps
 */
var event_manager_gmarkers = [], markerTimeout;
function initOnthemaps()
{
	centerMapsFromIP();
	
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

function getRadiusFromViewport()
{
	var bounds = event_manager_gmap.getBounds(), southWest = bounds.getSouthWest(), northEast = bounds.getNorthEast();

	diffLat = (northEast.lat()-southWest.lat());
	diffLng = (northEast.lng()-southWest.lng());

	test = new GLatLng(southWest.lat(), southWest.lng());
	test2 = new GLatLng(southWest.lat(), northEast.lng());

	radius = (test.distanceFrom(test2)/1000);
	return (radius*0.70);
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

function openInfowindow(i)
{
	
	GEvent.trigger(event_manager_gmarkers[i],"click");
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
		//gmarker.openInfoWindowHtml(text);
	});
	
	return gmarker;
}

function event_manager_program_add_day(form){
	$(form).find("input[type='submit']").hide();
	
	$.post('<?php echo EVENT_MANAGER_BASEURL; ?>/proc/day/edit', $(form).serialize(), function(response)
	{
		if(response.valid)
		{
			$.fancybox.close();
			guid = response.guid;
			if(response.edit){
				$("#day_" + guid + " .event_manager_program_day_details").html(response.content_body);
				$("#event_manager_event_view_program a[rel='day_" + guid + "']").html(response.content_title).click();
			} else {
				$("#event_manager_event_view_program").after(response.content_body);
				$("#event_manager_event_view_program li:last").before(response.content_title);
				$("#event_manager_event_view_program a[rel='day_" + guid + "']").click();
			}
		}
		else
		{
			$(form).find("input[type='submit']").show();
		}
	}, 'json');
}

function event_manager_program_add_slot(form){
	$(form).find("input[type='submit']").hide();
	
	$.post('<?php echo EVENT_MANAGER_BASEURL; ?>/proc/slot/edit', $(form).serialize(), function(response)
	{
		if(response.valid)
		{
			$.fancybox.close();
			
			guid = response.guid;
			parent_guid = response.parent_guid;
			if(response.edit){
				$("#" + guid).replaceWith(response.content);
			} else {
				$("#day_" + parent_guid).find("a.event_manager_program_slot_add").before(response.content);
			}
		}
		else
		{
			$(form).find("input[type='submit']").show();
		}
	}, 'json');
}

function event_manager_registrationform_add_field(form)
{
	$(form).find("input[type='submit']").hide();
	
	$.post('<?php echo EVENT_MANAGER_BASEURL; ?>/proc/question/edit', $(form).serialize(), function(response)
	{
		if(response.valid)
		{
			$.fancybox.close();
			guid = response.guid;

			if(response.edit)
			{
				$('#question_'+guid).html(response.content);
			}
			else
			{
				$("#event_manager_registrationform_fields").append(response.content);
				
				save_registrationform_question_order();
			}
		}
		else
		{
			$(form).find("input[type='submit']").show();
		}
	}, 'json');
}

function event_manager_execute_search(){
	$("#event_manager_result_refreshing").show();
	if($('#past_events').is(":hidden") == true)
	{
		var formData = $("#event_manager_search_form").serialize();
	}
	else
	{
		var formData = $($("#event_manager_search_form")[0].elements).not($("#event_manager_event_search_advanced_container")[0].children).serialize();
	}

	map_data_only = false;
	if($("#event_manager_result_navigation li.selected a").attr("rel") == "onthemap"){
		map_data_only = true;
	}
	
	
	$.post('<?php echo EVENT_MANAGER_BASEURL; ?>/proc/search/events', formData, function(response)
	{
		if(response.valid)
		{
			if(map_data_only)
			{
				event_manager_gmap.clearOverlays();
				zooming = true;
				event_manager_gmarkers = [];
				if(response.markers)
				{
					var bounds = event_manager_gmap.getBounds(), southWest = bounds.getSouthWest(), northEast = bounds.getNorthEast();
					
					$.each(response.markers, function(i, event)
					{
						marker = addMarkers(event.lat, event.lng, event.title, event.html, event.hasrelation, event.iscreator);
						
						event_manager_gmap.addOverlay(marker);
						event_manager_gmarkers.push(marker);
					});
				}
				makeSidebar();
			}
			else
			{
				$('#event_manager_event_list_search_more').remove();
				$('#event_manager_event_listing').html(response.content);
				$("#event_manager_result_refreshing").hide();
			}			
		}
		$("#event_manager_result_refreshing").hide();
	}, 'json');
}

function save_registrationform_question_order()
{
	var $sortableRegistrationForm = $('#event_manager_registrationform_fields');
	order = $sortableRegistrationForm.sortable('serialize');
	$.getJSON('<?php echo EVENT_MANAGER_BASEURL; ?>/proc/question/saveorder', order, function(response)
	{
		if(!response.valid)
		{
			alert('<?php echo elgg_echo('event_manager:registrationform:fieldorder:error');?>');
		}
	});
}


$(function()
{
	$('#event_manager_registrationform_fields').sortable({
		handle: 'span.move',
		placeholder: "event_manager_registrationform_field_placeholder",
		containment: 'parent',
		distance: 1,
		tolerance: 'pointer',
		update: function(event, ui) 
		{
			save_registrationform_question_order();
		}
	});
	
	var fancyboxOptions = {
			'onComplete': function()
			{
				var location = $('#event_manager_event_edit input[name="location"]').val();
				initMaps('map_canvas');
				initEditeventmaps();

				latlng = getLatLngFromFields();
				
				if(location != '')
				{
					coords = getCoordsFromAddress(location);
				}
				else if(latlng != null)
				{
					addMarker(latlng, true);
					getAdressFromCoords();
				}
			}
		};
	

	$('.openRouteToEvent').click(function(e)
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

/* Event Manager Search Form */
$(function()
{
	$('#event_manager_event_search_advanced_enable').click(function(){
		$('#event_manager_event_search_advanced_container, #past_events, #event_manager_event_search_advanced_enable span').toggle();		
	});
	
	$('#event_manager_event_list_search_more').live('click', function()
	{
		clickedElement = $(this);
		clickedElement.html('');
		clickedElement.addClass('event_manager_search_load');
		offset = parseInt($(this).attr('rel'), 10);
		
		$("#event_manager_result_refreshing").show();
		if($('#past_events').is(":hidden") == true)
		{
			var formData = $("#event_manager_search_form").serialize();
		}
		else
		{
			var formData = $($("#event_manager_search_form")[0].elements).not($("#event_manager_event_search_advanced_container")[0].children).serialize();
		}
		
		$.post('<?php echo EVENT_MANAGER_BASEURL; ?>/proc/search/events?offset='+offset, formData, function(response)
		{
			if(response.valid)
			{
				$('#event_manager_event_list_search_more').remove();
				$(response.content).insertAfter('.search_listing:last');
			}
			$("#event_manager_result_refreshing").hide();
		}, 'json');
	});
	
	$('#event_manager_search_form').submit(function(e)
	{
		event_manager_execute_search();
		e.preventDefault();
	});
	
	$("#event_manager_result_navigation li a").click(function()
	{
		if(!($(this).parent().hasClass("selected"))){
			selected = $(this).attr("rel");

			$("#event_manager_result_navigation li").toggleClass("selected");
			$("#event_manager_event_map, #event_manager_event_listing").toggle();
			
			if(selected == "onthemap"){
				initMaps('onthemap_canvas');
				initOnthemaps();
			} else {
				$("#event_manager_onthemap_sidebar").remove();
			}
			
			$('#search_type').val(selected);

			event_manager_execute_search();
		}
	});

	$('.event_manager_registration_approve').click(function()
	{
		regElmnt = $(this);
		regId = regElmnt.attr('rel');

		$.getJSON('<?php echo EVENT_MANAGER_BASEURL; ?>/proc/registration/approve', {guid: regId}, function(response)
		{
			if(response.valid)
			{
				regElmnt.unbind('click');
				regElmnt.replaceWith('<img border="0" src="/mod/event_manager/_graphics/icons/check_icon.png" />');
			}
		});
	});
	
	$('.event_manager_program_day_add').live('click', function()
	{
		eventGuid = $(this).attr("rel");
		$.fancybox({'href': '<?php echo EVENT_MANAGER_BASEURL; ?>/program/day?event_guid=' + eventGuid});
		
		return false;
	});

	$('.event_manager_program_day_edit').live('click', function()
	{
		guid = $(this).attr("rel");
		$.fancybox({'href': '<?php echo EVENT_MANAGER_BASEURL; ?>/program/day?day_guid=' + guid});
		
		return false;
	});
	
	$('.event_manager_program_slot_add').live('click', function()
	{
		dayGuid = $(this).attr("rel");
		$.fancybox({'href': '<?php echo EVENT_MANAGER_BASEURL; ?>/program/slot?day_guid=' + dayGuid});
		
		return false;
	});

	$('.event_manager_program_slot_edit').live('click', function()
	{
		guid = $(this).attr("rel");
		$.fancybox({'href': '<?php echo EVENT_MANAGER_BASEURL; ?>/program/slot?slot_guid=' + guid});
		
		return false;
	});
	
	$('#event_manager_questions_add').click(function()
	{
		eventGuid = $(this).attr("rel");
		$.fancybox({'href': '<?php echo EVENT_MANAGER_BASEURL; ?>/registrationform/question?event_guid=' + eventGuid});

		return false;
	});

	$('.event_manager_questions_edit').live('click', function()
	{
		guid = $(this).attr("rel");
		$.fancybox({'href': '<?php echo EVENT_MANAGER_BASEURL; ?>/registrationform/question?question_guid=' + guid});
		
		return false;
	});
	
	$('#event_manager_registrationform_question_fieldtype').live('change', function()
	{
		if($('#event_manager_registrationform_question_fieldtype').val() == 'Radiobutton' || $('#event_manager_registrationform_question_fieldtype').val() == 'Dropdown')
		{
			$('#event_manager_registrationform_select_options').show();
		}
		else
		{
			$('#event_manager_registrationform_select_options').hide();
		}
	});

	$('#event_manager_event_register_submit').click(function()
	{
		guids = [];
		$.each($('.event_manager_program_participatetoslot'), function(i, value)
		{
			elementId = $(value).attr('id');
			if($(value).is(':checked'))
			{
				guids.push(elementId.substring(9, elementId.length));
			}
		});

		$('#event_manager_program_guids').val(guids.join(','));

		$('#event_manager_event_register').submit();
	});
});
<!-- End Event manager -->