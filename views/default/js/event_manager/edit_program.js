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

				guid = json.output.guid;
				parent_guid = json.output.parent_guid;

				if (json.output.edit) {
					$("#" + guid).replaceWith(json.output.content);
				} else {
					$("#day_" + parent_guid).find(".event_manager_program_slot_add").before(json.output.content);
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
			guid = json.output.guid;
			if (guid) {
				$.colorbox.close();

				if(json.output.edit){
					$("#day_" + guid + " .event_manager_program_day_details").html(json.output.content_body);
					$("#event_manager_event_view_program a[rel='day_" + guid + "']").html(json.output.content_title).click();
				} else {
					$("#event_manager_event_view_program").after(json.output.content_body);
					$("#event_manager_event_view_program li:last").before(json.output.content_title);
					$("#event_manager_event_view_program a[rel='day_" + guid + "']").click();
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
		$("#event_manager_form_program_slot input[name='slot_set']").removeAttr("checked");
		$options = $("#event_manager_form_program_slot input[name='slot_set']:first").parent().parent().parent();
		$options.append("<li><label><input type='radio' checked='checked' value='" + set_name + "' name='slot_set'/>" + set_name + "</label></li>");
	}
};

elgg.event_manager.init_edit_program = function() {
	$(document).on('click', '.event_manager_program_day_delete', function(e) {
		if (!confirm(elgg.echo('deleteconfirm'))) {
			return false;
		}
		
		dayGuid = $(this).parent().attr("rel");
		if (!dayGuid) {
			return false;
		}
		
		$dayElements = $("#day_" + dayGuid + ", #event_manager_event_view_program li.elgg-state-selected");
		$dayElements.hide();

		elgg.action('event_manager/day/delete', {
			data: {
				guid: dayGuid
			},
			success: function(json) {
				if (json.status >= 0) {
					$dayElements.remove();
					if($("#event_manager_event_view_program li").length > 1){
						$("#event_manager_event_view_program li:first a").click();
					}
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
		
		slotGuid = $(this).parent().attr("rel");
		if (!slotGuid) {
			return false;
		}
		
		$slotElement = $("#" + slotGuid);
		$slotElement.hide();

		elgg.action('event_manager/slot/delete', {
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