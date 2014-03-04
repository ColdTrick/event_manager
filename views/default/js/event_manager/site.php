<?php ?>
//<script>
elgg.provide("elgg.event_manager");
var infowindow = null;

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

    // toggle drop down menu
    $(".event_manager_event_actions").live("click", function(event){
        if($(this).next().is(":hidden")){
            // only needed if the current menu is already dropped down
	    	$("body > .event_manager_event_actions_drop_down").remove();
			$("body").append($(this).next().clone());
			css_top = $(this).offset().top + $(this).height();
			css_left = $(this).offset().left;
			$("body > .event_manager_event_actions_drop_down").css({top: css_top, left: css_left}).show();;
        }
        
		event.stopPropagation();
    });

    // hide drop down menu items
    $("body").live("click", function(){
    	$("body > .event_manager_event_actions_drop_down").remove();
    });

}());

function event_manager_program_add_day(form){
	$(form).find("input[type='submit']").hide();
	
	$.post(elgg.get_site_url() + 'events/proc/day/edit', $(form).serialize(), function(response) {
		if(response.valid) {
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
		} else {
			$(form).find("input[type='submit']").show();
		}
	}, 'json');
}

function event_manager_program_add_slot(form){
	$(form).find("input[type='submit']").hide();
	
	$.post(elgg.get_site_url() + 'events/proc/slot/edit', $(form).serialize(), function(response) {
		if(response.valid) {
			$.fancybox.close();
			
			guid = response.guid;
			parent_guid = response.parent_guid;
			if(response.edit){
				$("#" + guid).replaceWith(response.content);
			} else {
				$("#day_" + parent_guid).find("a.event_manager_program_slot_add").before(response.content);
			}
		} else {
			$(form).find("input[type='submit']").show();
		}
	}, 'json');
}

function event_manager_registrationform_add_field(form) {
	$(form).find("input[type='submit']").hide();
	
	$.post(elgg.get_site_url() + 'events/proc/question/edit', $(form).serialize(), function(response){
		if(response.valid) {
			$.fancybox.close();
			guid = response.guid;

			if(response.edit) {
				$('#question_' + guid).replaceWith(response.content);
			} else {
				$("#event_manager_registrationform_fields").append(response.content);
				
				save_registrationform_question_order();
			}
		} else {
			$(form).find("input[type='submit']").show();
		}
	}, 'json');
}

function event_manager_execute_search(){
	
	$("#event_manager_result_refreshing").show();
	
	map_data_only = false;
	if($("#event_manager_result_navigation li.elgg-state-selected a").attr("rel") == "onthemap"){
		map_data_only = true;
		mapBounds = event_manager_gmap.getBounds();
		latitude = mapBounds.getCenter().lat();
		longitude = mapBounds.getCenter().lng();
		distance_latitude = mapBounds.getNorthEast().lat() - latitude;
		distance_longitude = mapBounds.getNorthEast().lng() - longitude;
		if(distance_longitude < 0){
			distance_longitude = 360 + distance_longitude;
		}

		$("#latitude").val(latitude);
		$("#longitude").val(longitude);
		$("#distance_latitude").val(distance_latitude);
		$("#distance_longitude").val(distance_longitude);
	}
	
	var formData = $("#event_manager_search_form").serialize();
	
	$.post(elgg.get_site_url() + 'events/proc/search/events', formData, function(response){
		if(response.valid){
		
			if(map_data_only) {
				
				if(response.markers) {
					
					infowindow = new google.maps.InfoWindow();
					
					var shadowIcon = new google.maps.MarkerImage("//chart.apis.google.com/chart?chst=d_map_pin_shadow",
					        new google.maps.Size(40, 37),
					        new google.maps.Point(0, 0),
					        new google.maps.Point(12, 35));
			        var ownIcon = "//maps.google.com/mapfiles/ms/icons/yellow-dot.png";
			        var attendingIcon = "//maps.google.com/mapfiles/ms/icons/blue-dot.png";
										
					$.each(response.markers, function(i, event) {
						existing = false;
						if (event_manager_gmarkers) {
							if(event_manager_gmarkers[event.guid]){
								existing = true;
						    }
					  	}
					  	if(!existing){
							var myLatlng = new google.maps.LatLng(event.lat, event.lng);
	
							markerOptions = {
									map: event_manager_gmap,
									position: myLatlng,
									animation: google.maps.Animation.DROP,
									title: event.title,
									shadow: shadowIcon
								};
							if(event.iscreator){
								markerOptions.icon = ownIcon;
							} else {
								if(event.has_relation){
									markerOptions.icon = attendingIcon;
								}
							}
							var marker = new google.maps.Marker(markerOptions);
							
							google.maps.event.addListener(marker, 'click', function() {
								infowindow.setContent(event.html);
							  	infowindow.open(event_manager_gmap,marker);
							});
												
							event_manager_gmarkers[event.guid] = marker;
					  	}
					});
				}

				// make sidebar
				//getMarkersJson();
			} else {
				$('#event_manager_event_list_search_more').remove();
				$('#event_manager_event_listing').html(response.content);
				$("#event_manager_result_refreshing").hide();
			}
		}
		
		$("#event_manager_result_refreshing").hide();
	}, 'json');
}

function save_registrationform_question_order() {
	var $sortableRegistrationForm = $('#event_manager_registrationform_fields');
	order = $sortableRegistrationForm.sortable('serialize');
	$.getJSON(elgg.get_site_url() + 'events/proc/question/saveorder', order, function(response){
		if(!response.valid)	{
			alert(elgg.echo('event_manager:registrationform:fieldorder:error'));
		}
	});
}

elgg.event_manager.slot_set_init = function() {
	$form = $("#event_manager_event_register");
	if ($form.length > 0) {
		set_names = []; // store processed set names
		
		$form.find(".event_manager_program_participatetoslot[rel]:checked").each(function(){
			rel = $(this).attr("rel");
			if ($.inArray(rel, set_names) < 0) {
				set_names.push[rel];
				$form.find(".event_manager_program_participatetoslot[rel='" + rel + "'][id!='" + $(this).attr("id") + "']").removeAttr("checked").attr("disabled", "disabled");
			}
		});

		$form.find(".event_manager_program_participatetoslot[rel]").live("change", function(){
			rel = $(this).attr("rel");
			selected_id = $form.find(".event_manager_program_participatetoslot[rel='" + rel + "']:checked:first").attr("id");
			if(selected_id){
				// disabled others
				$form.find(".event_manager_program_participatetoslot[rel='" + rel + "'][id!='" + selected_id + "']").removeAttr("checked").attr("disabled", "disabled");
			} else {
				// enable others
				$form.find(".event_manager_program_participatetoslot[rel='" + rel + "']").removeAttr("checked").removeAttr("disabled");
			}
		});
	}
}

elgg.event_manager.search_attendees = function(q) {
	if(q === ""){
		$(".event-manager-event-view-attendee-info").show();
	} else {
		$(".event-manager-event-view-attendee-info").hide();
		$(".event-manager-event-view-attendee-info").each(function(){
			if ($(this).attr("rel").toUpperCase().indexOf(q.toUpperCase()) >= 0) {
				$(this).show();
			}
		});
	}
}

elgg.event_manager.add_new_slot_set_name = function(set_name) {
	if(set_name !== ""){
		$("#event_manager_form_program_slot input[name='slot_set']").removeAttr("checked");
		$options = $("#event_manager_form_program_slot input[name='slot_set']:first").parent().parent().parent();
		$options.append("<li><label><input type='radio' checked='checked' value='" + set_name + "' name='slot_set'/>" + set_name + "</label></li>");
	}
}

elgg.event_manager.init = function() {

	elgg.event_manager.slot_set_init();
	
	$('.event_manager_program_slot_delete').live('click', function() {
		if(confirm(elgg.echo('deleteconfirm'))) {
			slotGuid = $(this).parent().attr("rel");
			if(slotGuid) {
				$slotElement = $("#" + slotGuid);
				$slotElement.hide();
				$.getJSON(elgg.get_site_url() + 'events/proc/slot/delete', {guid: slotGuid}, function(response) {
					if(response.valid) {
						$slotElement.remove();
					} else {
						$slotElement.show();
					}
				});
			}
		}
		return false;
		
	});

	$('.event_manager_program_day_delete').live('click', function(e) {
		if(confirm(elgg.echo('deleteconfirm'))) {
			dayGuid = $(this).parent().attr("rel");
			if(dayGuid) {
				$dayElements = $("#day_" + dayGuid + ", #event_manager_event_view_program li.elgg-state-selected");
				$dayElements.hide();
				$.getJSON(elgg.get_site_url() + 'events/proc/day/delete', {guid: dayGuid}, function(response) {
					if(response.valid) {
						// remove from DOM
						$dayElements.remove();
						if($("#event_manager_event_view_program li").length > 1){
							$("#event_manager_event_view_program li:first a").click();
						}
					} else {
						// revert
						$dayElements.show();
					}
				});
			}
		}
		
		return false;
	});

	$('.event_manager_questions_delete').live('click', function(e) {
		if(confirm(elgg.echo('deleteconfirm'))) {
			questionGuid = $(this).attr("rel");
			if(questionGuid) {
				$questionElement = $(this);
				$questionElement.parent().hide();
				$.getJSON(elgg.get_site_url() + 'events/proc/question/delete', {guid: questionGuid}, function(response) {
					if(response.valid) {
						// remove from DOM
						$questionElement.parent().remove();
					} else {
						// revert
						$questionElement.parent().show();
					}
				});
			}
		}
		
		return false;
	});
	
	/* Event Manager Search Form */
	$('#event_manager_registrationform_fields').sortable({
		axis: 'y',
		tolerance: 'pointer',
		opacity: 0.8,
		forcePlaceholderSize: true,
		forceHelperSize: true,
		update: function(event, ui)	{
			save_registrationform_question_order();
		}
	});
	
	$('#event_manager_event_search_advanced_enable').click(function()
	{
		$('#event_manager_event_search_advanced_container, #past_events, #event_manager_event_search_advanced_enable span').toggle();

		if($('#past_events').is(":hidden"))
		{
			console.log('advanced');
			$('#advanced_search').val('1');
		}
		else
		{
			console.log('simple');
			$('#advanced_search').val('0');
		}
	});
	
	$('#event_manager_event_list_search_more').live('click', function()	{
		clickedElement = $(this);
		clickedElement.html('<div class="elgg-ajax-loader"></div>');
		offset = parseInt($(this).attr('rel'), 10);
		
		$("#event_manager_result_refreshing").show();
		if($('#past_events').is(":hidden") == true) {
			var formData = $("#event_manager_search_form").serialize();
		} else {
			var formData = $($("#event_manager_search_form")[0].elements).not($("#event_manager_event_search_advanced_container")[0].children).serialize();
		}
		
		$.post(elgg.get_site_url() + 'events/proc/search/events?offset='+offset, formData, function(response) {
			if(response.valid) {
				$('#event_manager_event_list_search_more').remove();
				//$(response.content).insertAfter('.search_listing:last');
				$('#event_manager_event_listing').append(response.content);
			}
			$("#event_manager_result_refreshing").hide();
		}, 'json');
	});
	
	$('#event_manager_search_form').submit(function(e) {
		event_manager_execute_search();
		e.preventDefault();
	});
	
	$("#event_manager_result_navigation li a").click(function() {
		if(!($(this).parent().hasClass("elgg-state-selected"))){
			selected = $(this).attr("rel");

			$("#event_manager_result_navigation li").toggleClass("elgg-state-selected");
			$("#event_manager_event_map, #event_manager_event_listing").toggle();

			$('#search_type').val(selected);
			
			if(selected == "onthemap"){
				initMaps('event_manager_onthemap_canvas', true);
			} else {
				$("#event_manager_onthemap_sidebar").remove();
				event_manager_execute_search();
			}
		}
	});

	$('.event_manager_registration_approve').click(function() {
		regElmnt = $(this);
		regId = regElmnt.attr('rel');

		$.getJSON(elgg.get_site_url() + 'events/proc/registration/approve', {guid: regId}, function(response) {
			if(response.valid) {
				regElmnt.unbind('click');
				regElmnt.replaceWith('<img border="0" src="/mod/event_manager/_graphics/icons/check_icon.png" />');
			}
		});
	});
	
	$('.event_manager_program_day_add').live('click', function() {
		eventGuid = $(this).attr("rel");
		$.fancybox({
				'href': elgg.get_site_url() + 'events/program/day?event_guid=' + eventGuid,
				'onComplete'		: function() {
						elgg.ui.initDatePicker();
					}
			});
		
		return false;
	});

	$('.event_manager_program_day_edit').live('click', function() {
		guid = $(this).attr("rel");
		$.fancybox({
				'href': elgg.get_site_url() + 'events/program/day?day_guid=' + guid,
				'onComplete'		: function() {
					elgg.ui.initDatePicker();
				}
			});
		
		return false;
	});
	
	$('.event_manager_program_slot_add').live('click', function() {
		dayGuid = $(this).attr("rel");
		$.fancybox({'href': elgg.get_site_url() + 'events/program/slot?day_guid=' + dayGuid});
		
		return false;
	});

	$('.event_manager_program_slot_edit').live('click', function() {
		guid = $(this).attr("rel");
		$.fancybox({'href': elgg.get_site_url() + 'events/program/slot?slot_guid=' + guid});
		
		return false;
	});
	
	$('#event_manager_questions_add').click(function() {
		eventGuid = $(this).attr("rel");
		$.fancybox({'href': elgg.get_site_url() + 'events/registrationform/question?event_guid=' + eventGuid});

		return false;
	});

	$('.event_manager_questions_edit').live('click', function()	{
		guid = $(this).attr("rel");
		$.fancybox({'href': elgg.get_site_url() + 'events/registrationform/question?question_guid=' + guid});
		
		return false;
	});
	
	$('#event_manager_registrationform_question_fieldtype').live('change', function() {
		if($('#event_manager_registrationform_question_fieldtype').val() == 'Radiobutton' || $('#event_manager_registrationform_question_fieldtype').val() == 'Dropdown') {
			$('#event_manager_registrationform_select_options').show();
		} else {
			$('#event_manager_registrationform_select_options').hide();
		}
	});

	$('#event_manager_event_register').submit(function() {
		if(($("input[name='question_name']").val() == "") || ($("input[name='question_email']").val() == "")){
			elgg.register_error(elgg.echo("event_manager:registration:required_fields"));
			return false;
		}

		error_found = false;
		
		$("#event_manager_registration_form_fields .required").each(function(index, elem){
			if($(this).hasClass("elgg-input-radios")){
				if($(this).find("input[type='radio']:checked").length == 0){
					error_found = true;
					return false;
				}
			} else if($(this).val() == ""){
				error_found = true;
				return false;
				
			}
		});
		if(error_found){
			elgg.register_error(elgg.echo("event_manager:registration:required_fields"));
			
			return false;
		}
		
		guids = [];
		$.each($('.event_manager_program_participatetoslot'), function(i, value) {
			elementId = $(value).attr('id');
			if($(value).is(':checked')) {
				guids.push(elementId.substring(9, elementId.length));
			}
		});

		$('#event_manager_program_guids').val(guids.join(','));
	});
	
	$('#with_program').change(function() {
		if($(this).is(':checked')) {
			$('#event_manager_start_time_pulldown').css('display', 'none');
		} else {
			$('#event_manager_start_time_pulldown').css('display', 'table-row');
		}
	});

	$("#event-manager-event-view-search-attendees").live("keyup", function(){
		elgg.event_manager.search_attendees($(this).val());
	});

	$("#event-manager-new-slot-set-name-button").live("click", function(){
		elgg.event_manager.add_new_slot_set_name($("#event-manager-new-slot-set-name").val());
	});
};

elgg.register_hook_handler('init', 'system', elgg.event_manager.init);