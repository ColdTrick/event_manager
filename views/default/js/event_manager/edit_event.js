elgg.provide('elgg.event_manager');

elgg.event_manager.edit_event_init = function() {

	$('#event_manager_event_edit input[name="registration_needed[]"]').on('change', function() {
		if ($(this).val()) {
			$('#event-manager-forms-event-edit-questions, .elgg-menu-item-event-edit-questions').removeClass('hidden');
		}		
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

	$(document).on('click', '#event-manager-location-input-delete', function() {
		$('#event_manager_event_edit').find('input[name="location"], input[name="latitude"], input[name="longitude"]').val('');
		$(this).closest('.elgg-field').addClass('hidden');
	});
};

elgg.register_hook_handler('init', 'system', elgg.event_manager.edit_event_init);
