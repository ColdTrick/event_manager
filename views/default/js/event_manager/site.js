elgg.provide("elgg.event_manager");

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

		$(document).on('change', '#event_manager_event_register .event_manager_program_participatetoslot[rel]', function() {
			$form = $("#event_manager_event_register");
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
};

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
};

elgg.event_manager.init = function() {

	elgg.event_manager.slot_set_init();

	// toggle drop down menu
	$(document).on('click', '.event_manager_event_actions', function(event) {
		if ($(this).next().is(':hidden')) {
			// only needed if the current menu is already dropped down
			$('body > .event_manager_event_actions_drop_down').remove();
			$('body').append($(this).next().clone());
			css_top = $(this).offset().top + $(this).height();
			css_left = $(this).offset().left;
			$('body > .event_manager_event_actions_drop_down').css({top: css_top, left: css_left}).show();
		}

		event.stopPropagation();
	});

	// hide drop down menu items
	$(document).on('click', function() {
		$('body > .event_manager_event_actions_drop_down').remove();
	});
	
	$('#event_manager_event_register').submit(function() {
		if (($("input[name='question_name']").val() === "") || ($("input[name='question_email']").val() === "")) {
			elgg.register_error(elgg.echo("event_manager:registration:required_fields"));
			return false;
		}

		var error_found = false;

		$("#event_manager_registration_form_fields .required").each(function(index, elem){
			if ($(this).hasClass("elgg-input-radios")) {
				if ($(this).find("input[type='radio']:checked").length === 0) {
					error_found = true;
					return false;
				}
			} else if($(this).val() === "") {
				error_found = true;
				return false;
			}
		});

		if (error_found) {
			elgg.register_error(elgg.echo("event_manager:registration:required_fields"));
			return false;
		}

		var guids = [];
		$.each($('.event_manager_program_participatetoslot'), function(i, value) {
			elementId = $(value).attr('id');
			if ($(value).is(':checked')) {
				guids.push(elementId.substring(9, elementId.length));
			}
		});

		$('#event_manager_program_guids').val(guids.join(','));
	});

	$(document).on('keyup', '#event-manager-event-view-search-attendees', function(){
		elgg.event_manager.search_attendees($(this).val());
	});
};

elgg.register_hook_handler('init', 'system', elgg.event_manager.init);