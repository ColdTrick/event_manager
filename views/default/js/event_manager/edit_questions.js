elgg.provide('elgg.event_manager');

elgg.event_manager.edit_questions_add_field = function(form) {
	$(form).find("input[type='submit']").hide();
	var guid = $(form).find('input[name="question_guid"]').val();
	
	elgg.action('event_manager/question/edit', {
		data: $(form).serialize(), 
		success: function(data) {
			$.colorbox.close();
			
			if(guid) {
				$('#question_' + guid).replaceWith(data.output);
			} else {
				$('#event_manager_registrationform_fields').append(data.output);

				elgg.event_manager.edit_questions_save_order();
			}
		},
		error: function() {
			$(form).find("input[type='submit']").show();
		}
	});
};

elgg.event_manager.edit_questions_save_order = function() {
	var order = $('#event_manager_registrationform_fields').sortable('serialize');
	elgg.action('event_manager/question/save_order?' + order);
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
	
	$(document).on('click', '.event_manager_questions_delete', function(e) {
		if (!confirm(elgg.echo('deleteconfirm'))) {
			return false;
		}
		
		var questionGuid = $(this).attr("rel");
		if (!questionGuid) {
			return false;
		}
		
		$questionElement = $(this);
		$questionElement.parent().hide();
		
		elgg.action('event_manager/question/delete', {
			data: {
				guid: questionGuid
			}, 
			success: function(data) {
				// remove from DOM
				$questionElement.parent().remove();
			},
			error: function() {
				// revert
				$questionElement.parent().show();
			}
		});
	});

	$(document).on('change', '#event_manager_registrationform_question_fieldtype', function() {
		var type = $(this).val();
		if (type == 'Radiobutton' || type == 'Dropdown') {
			$('#event_manager_registrationform_select_options').show();
		} else {
			$('#event_manager_registrationform_select_options').hide();
		}
	});
};

elgg.register_hook_handler('init', 'system', elgg.event_manager.edit_questions_init);