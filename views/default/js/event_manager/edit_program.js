elgg.provide('elgg.event_manager');

elgg.event_manager.program_add_slot = function(event) {
	event.preventDefault();

	var $button = $(this).find("input[type='submit']");

	// Prevent accidental double click
	$button.hide();

	var url = $(this).attr('action');
	var data = $(this).serialize();

	elgg.action(url, {
		data: data,
		success: function(json) {
			if (json.status === 0) {
				$.colorbox.close();

				if (json.output.edit) {
					$("#" + json.output.guid).replaceWith(json.output.content);
				} else {
					$("#day_" + json.output.parent_guid).find(".event_manager_program_slot_add").before(json.output.content);
				}
			} else {
				$button.show();
			}
		}
	});
};

elgg.event_manager.program_add_day = function(form) {
	var $button = $(form).find("input[type='submit']");
	$button.hide();

	elgg.action('event_manager/day/edit', {
		data: $(form).serialize(),
		success: function(json) {
			var guid = json.output.guid;
			if (guid) {
				$.colorbox.close();

				if(json.output.edit){
					$("#day_" + guid + " .event_manager_program_day_details").html(json.output.content_body);
					$("#event_manager_event_view_program a[rel='day_" + guid + "']").html(json.output.content_title).click();
				} else {
					var $program = $('#event_manager_event_view_program');
					$program.find('.elgg-tabs-content').append(json.output.content_body);
					$program.find('.elgg-menu-navigation-tabs').append(json.output.content_title);
					$program.find("a[rel='day_" + guid + "']").click();
				}
			} else {
				$button.show();
			}
		},
		error: function() {
			$button.show();
		}
	});
};

elgg.event_manager.add_new_slot_set_name = function(set_name) {
	if (set_name !== "") {
		$("#event_manager_form_program_slot input[name='slot_set']").prop("checked", false);
		var $options = $("#event_manager_form_program_slot input[name='slot_set']:first").parent().parent().parent();
		$options.append("<li><label><input type='radio' checked='checked' value='" + set_name + "' name='slot_set'/>" + set_name + "</label></li>");
	}
};

elgg.event_manager.init_edit_program = function() {
	$(document).on('click', '.event_manager_program_day_delete', function() {
		if (!confirm(elgg.echo('deleteconfirm'))) {
			return false;
		}
		
		var dayGuid = $(this).parent().attr("rel");
		if (!dayGuid) {
			return false;
		}
		
		var $program = $('#event_manager_event_view_program');
		
		var $dayElements = $program.find("#day_" + dayGuid + ", .elgg-menu-navigation-tabs a[rel='day_" + dayGuid + "']").parent();
		
		$program.find('.elgg-menu-navigation-tabs li:first a').click();
		
		$dayElements.hide();

		elgg.action('entity/delete', {
			data: {
				guid: dayGuid
			},
			success: function(json) {
				if (json.status >= 0) {
					$dayElements.remove();
				} else {
					$dayElements.show();
				}
			}
		});
	});
	
	$(document).on('click', '.event_manager_program_slot_delete', function() {
		if (!confirm(elgg.echo('deleteconfirm'))) {
			return false;
		}
		
		var slotGuid = $(this).parent().attr("rel");
		if (!slotGuid) {
			return false;
		}
		
		var $slotElement = $("#" + slotGuid);
		$slotElement.hide();

		elgg.action('entity/delete', {
			data: {
				guid: slotGuid
			},
			success: function(json) {
				if (json.status >= 0) {
					$slotElement.remove();
				} else {
					$slotElement.show();
				}
			}
		});
	});
	
	$(document).on('click', '#event-manager-new-slot-set-name-button', function(){
		elgg.event_manager.add_new_slot_set_name($("#event-manager-new-slot-set-name").val());
	});
	
	$(document).on('submit', '#event_manager_form_program_slot', elgg.event_manager.program_add_slot);
};

elgg.register_hook_handler('init', 'system', elgg.event_manager.init_edit_program);