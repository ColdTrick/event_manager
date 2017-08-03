elgg.provide('elgg.event_manager');

elgg.event_manager.edit_event_map_search = function() {
	var $search_form = $('#event-manager-edit-maps-search-container');
	elgg.event_manager.map.setLocation($search_form.find('input[name="address_search"]').val());
	$search_form.find('[name="address_search_save"]').removeClass('hidden');
};

elgg.event_manager.edit_event_init = function() {
	
	
	$('#event_manager_event_edit input[name="location"]').on('click', function() {
		var $elem = $(this);

		require(['elgg/lightbox'], function(lightbox) {
			lightbox.open({
				'inline': true,
				'href': '#event-manager-edit-maps-search-container',
				'onComplete': function () {
					
					var current_location = $elem.val();
					if (current_location) {
						$('#event-manager-edit-maps-search-container input[name="address_search"]').val(current_location);
						$('#event-manager-edit-maps-search-container [name="address_search_save"]').removeClass('hidden');
					}
					
					require(['event_manager/maps'], function (EventMap) {
						if (!elgg.event_manager.map) {
							elgg.event_manager.map = EventMap.setup('#event-manager-gmaps-location-search');
							elgg.event_manager.map.setLocation(current_location);
						}
					});
				}
			});
		});
	});
	
	$('#event_manager_event_edit input[name="registration_needed[]"]').on('change', function() {
		if ($(this).val()) {
			$('#event-manager-forms-event-edit-questions, .elgg-menu-item-event-edit-questions').removeClass('hidden');
		}		
	});
	
	$(document).on('keyup', '#event-manager-edit-maps-search-container input[name="address_search"]', function(event) {
		if (event.keyCode == 13) {
			elgg.event_manager.edit_event_map_search();
		} else {
			$('#event-manager-edit-maps-search-container [name="address_search_save"]').addClass('hidden');
		}
	});
	$(document).on('click', '#event-manager-edit-maps-search-container input[name="address_search_submit"]', function() {
		elgg.event_manager.edit_event_map_search();
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
		var $toggle_field = $(this).parent().parent().next().find('.elgg-field'); 
		$toggle_field.addClass('hidden');
		var entered_value = $(this).val().trim();
		if (entered_value && (entered_value !== '0')) {
			$toggle_field.removeClass('hidden');
		}
	});
	
	// Registration Questions
	$('.event_manager_registrationform_fields').sortable({
		axis: 'y',
		tolerance: 'pointer',
		opacity: 0.8,
		forcePlaceholderSize: true,
		forceHelperSize: true,
	});
	
	$(document).on('click', '.event_manager_questions_delete', function(e) {
		if (e.isDefaultPrevented()) {
			return;
		}
		$(this).parents('.elgg-item-object-eventregistrationquestion').eq(0).remove();
	});
	
	$(document).on('change', '.event_manager_registrationform_question_fieldtype', function() {
		var type = $(this).val();
		var $parent = $(this).parents('.elgg-item-object-eventregistrationquestion').eq(0);
		if (type == 'Radiobutton' || type == 'Dropdown') {
			$parent.find('.event_manager_registrationform_select_options').show();
		} else {
			$parent.find('.event_manager_registrationform_select_options').hide();
		}
	});
	
	$(document).on('click', '.event-manager-registration-add-field', function() {
		var $clone = $('#event-manager-registration-field-template').clone();
		$clone.appendTo('.event_manager_registrationform_fields').removeClass('hidden').removeAttr('id');
		$clone.find(':disabled').removeAttr('disabled');
		
		var d = new Date();
		var temp_id = 't_' + d.getTime();
		$($clone).html($clone.html().replace(/questions\[\]\[/g, 'questions[' + temp_id + ']['));	
	});
};

elgg.register_hook_handler('init', 'system', elgg.event_manager.edit_event_init);