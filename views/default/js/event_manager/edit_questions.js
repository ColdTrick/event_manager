elgg.provide('elgg.event_manager');

elgg.event_manager.edit_questions_add_field = function(form) {
	$(form).find("input[type='submit']").hide();

	$.post(elgg.get_site_url() + 'events/proc/question/edit', $(form).serialize(), function(response){
		if(response.valid) {
			$.colorbox.close();
			guid = response.guid;

			if(response.edit) {
				$('#question_' + guid).replaceWith(response.content);
			} else {
				$("#event_manager_registrationform_fields").append(response.content);

				elgg.event_manager.edit_questions_save_order();
			}
		} else {
			$(form).find("input[type='submit']").show();
		}
	}, 'json');
};

elgg.event_manager.edit_questions_save_order = function() {
	var $sortableRegistrationForm = $('#event_manager_registrationform_fields');
	order = $sortableRegistrationForm.sortable('serialize');
	$.getJSON(elgg.get_site_url() + 'events/proc/question/saveorder', order, function(response){
		if(!response.valid)	{
			alert(elgg.echo('event_manager:registrationform:fieldorder:error'));
		}
	});
};

elgg.event_manager.edit_questions_init = function() {
	
	$('#event_manager_registrationform_fields').sortable({
		axis: 'y',
		tolerance: 'pointer',
		opacity: 0.8,
		forcePlaceholderSize: true,
		forceHelperSize: true,
		update: function(event, ui)	{
			elgg.event_manager.edit_questions_save_order();
		}
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

	$('#event_manager_registrationform_question_fieldtype').live('change', function() {
		var type = $(this).val();
		if (type == 'Radiobutton' || type == 'Dropdown') {
			$('#event_manager_registrationform_select_options').show();
		} else {
			$('#event_manager_registrationform_select_options').hide();
		}
	});
};

elgg.register_hook_handler('init', 'system', elgg.event_manager.edit_questions_init);