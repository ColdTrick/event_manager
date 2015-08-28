elgg.provide('elgg.event_manager');

function event_manager_program_add_slot(event){
	event.preventDefault();

	var button = $(this).find("input[type='submit']");

	// Prevent accidental double click
	button.hide();

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
					$("#day_" + parent_guid).find("a.event_manager_program_slot_add").before(json.output.content);
				}
			} else {
				button.show();
			}
		}
	});
};

function event_manager_program_add_day(form){
	$(form).find("input[type='submit']").hide();

	$.post(elgg.get_site_url() + 'events/proc/day/edit', $(form).serialize(), function(response) {
		if(response.valid) {
			$.colorbox.close();
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
};

elgg.event_manager.add_new_slot_set_name = function(set_name) {
	if(set_name !== ""){
		$("#event_manager_form_program_slot input[name='slot_set']").removeAttr("checked");
		$options = $("#event_manager_form_program_slot input[name='slot_set']:first").parent().parent().parent();
		$options.append("<li><label><input type='radio' checked='checked' value='" + set_name + "' name='slot_set'/>" + set_name + "</label></li>");
	}
};

elgg.event_manager.init_edit_program = function() {
	$('.event_manager_program_day_add').live('click', function() {
		eventGuid = $(this).attr("rel");
		$.colorbox({
			'href': elgg.get_site_url() + 'events/program/day?event_guid=' + eventGuid,
			'onComplete': function() {
				elgg.ui.initDatePicker();
			}
		});

		return false;
	});

	$('.event_manager_program_day_edit').live('click', function() {
		guid = $(this).attr("rel");
		$.colorbox({
			'href': elgg.get_site_url() + 'events/program/day?day_guid=' + guid,
			'onComplete': function() {
				elgg.ui.initDatePicker();
			}
		});

		return false;
	});
	
	$('.event_manager_program_day_delete').live('click', function(e) {
		if(confirm(elgg.echo('deleteconfirm'))) {
			dayGuid = $(this).parent().attr("rel");
			if(dayGuid) {
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
			}
		}

		return false;
	});

	$('.event_manager_program_slot_add').live('click', function() {
		var dayGuid = $(this).attr("rel");
		$.colorbox({
			'href': elgg.get_site_url() + 'events/program/slot?day_guid=' + dayGuid
		});

		return false;
	});

	$('.event_manager_program_slot_edit').live('click', function() {
		var guid = $(this).attr("rel");
		$.colorbox({
			'href': elgg.get_site_url() + 'events/program/slot?slot_guid=' + guid
		});

		return false;
	});
	
	$('.event_manager_program_slot_delete').live('click', function() {
		if (confirm(elgg.echo('deleteconfirm'))) {
			slotGuid = $(this).parent().attr("rel");
			if (slotGuid) {
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
			}
		}
		return false;

	});
	
	$("#event-manager-new-slot-set-name-button").live("click", function(){
		elgg.event_manager.add_new_slot_set_name($("#event-manager-new-slot-set-name").val());
	});
	
	$('#event_manager_form_program_slot').live('submit', event_manager_program_add_slot);
};

elgg.register_hook_handler('init', 'system', elgg.event_manager.init_edit_program);