define(['jquery', 'elgg/i18n', 'elgg/system_messages'], function($, i18n, system_messages) {

	var $form = $("#event_manager_event_register");
	if ($form.length > 0) {
		var set_names = []; // store processed set names

		$form.find(".event_manager_program_participatetoslot[rel]:checked").each(function(){
			var rel = $(this).attr("rel");
			if ($.inArray(rel, set_names) < 0) {
				set_names.push[rel];
				$form.find(".event_manager_program_participatetoslot[rel='" + rel + "'][id!='" + $(this).attr("id") + "']").removeAttr("checked").attr("disabled", "disabled");
			}
		});

		$(document).on('change', '#event_manager_event_register .event_manager_program_participatetoslot[rel]', function() {
			var $form = $("#event_manager_event_register");
			var rel = $(this).attr("rel");
			var selected_id = $form.find(".event_manager_program_participatetoslot[rel='" + rel + "']:checked:first").attr("id");
			if (selected_id) {
				// disabled others
				$form.find(".event_manager_program_participatetoslot[rel='" + rel + "'][id!='" + selected_id + "']").removeAttr("checked").attr("disabled", "disabled");
			} else {
				// enable others
				$form.find(".event_manager_program_participatetoslot[rel='" + rel + "']").removeAttr("checked").removeAttr("disabled");
			}
		});
	}
	
	$(document).on('submit', '#event_manager_event_register', function() {
		if (($("input[name='question_name']").val() === "") || ($("input[name='question_email']").val() === "")) {
			system_messages.error(i18n.echo("event_manager:registration:required_fields"));
			return false;
		}

		var error_found = false;

		$("#event_manager_registration_form_fields .elgg-field-required [required]").each(function(index, elem){
			if ($(this).hasClass("elgg-input-radios")) {
				if ($(this).find("input[type='radio']:checked").length === 0) {
					error_found = true;
					return false;
				}
			} else if ($(this).val() === "") {
				error_found = true;
				return false;
			}
		});

		if (error_found) {
			system_messages.error(i18n.echo("event_manager:registration:required_fields"));
			return false;
		}

		var guids = [];
		$.each($('.event_manager_program_participatetoslot'), function(i, value) {
			var elementId = $(value).attr('id');
			if ($(value).is(':checked')) {
				guids.push(elementId.substring(9, elementId.length));
			}
		});

		$('#event_manager_program_guids').val(guids.join(','));
	});
});
