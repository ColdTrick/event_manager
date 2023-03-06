define(['jquery', 'elgg/i18n', 'elgg/Ajax', 'elgg/lightbox'], function($, i18n, Ajax) {

	var ajax = new Ajax();
	
	function addSlot(event) {
		event.preventDefault();
	
		var $button = $(this).find("input[type='submit']");
	
		// Prevent accidental double click
		$button.hide();
	
		var url = $(this).attr('action');
	
		ajax.action(url, {
			data: ajax.objectify($(this)),
			success: function(output) {
				$.colorbox.close();
	
				if (output.edit) {
					$("#" + output.guid).replaceWith(output.content);
				} else {
					$("#day_" + output.parent_guid).find(".event_manager_program_slot_add").before(output.content);
				}
			},
			error: function () {
				$button.show();
			}
		});
	};

	function addDay(event) {
		event.preventDefault();
		
		var $button = $(this).find("input[type='submit']");
		$button.hide();
	
		ajax.action('event_manager/day/edit', {
			data: ajax.objectify($(this)),
			success: function(output) {
				var guid = output.guid;
				if (guid) {
					$.colorbox.close();
	
					if (output.edit) {
						$("#day_" + guid + " .event_manager_program_day_details").html(output.content_body);
						$("#event_manager_event_view_program a[rel='day_" + guid + "']").html(output.content_title).click();
					} else {
						var $program = $('#event_manager_event_view_program');
						$program.find('.elgg-tabs-content').append(output.content_body);
						$program.find('.elgg-menu-navigation-tabs').append(output.content_title);
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
		
	$(document).on('click', '.event_manager_program_day_delete', function() {
		if (!confirm(i18n.echo('deleteconfirm'))) {
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

		ajax.action('entity/delete', {
			data: {
				guid: dayGuid
			},
			success: function(json) {
				$dayElements.remove();
			},
			error: function() {
				$dayElements.show();
			}
		});
	});
	
	$(document).on('click', '.event_manager_program_slot_delete', function() {
		if (!confirm(i18n.echo('deleteconfirm'))) {
			return false;
		}
		
		var slotGuid = $(this).parent().attr('rel');
		if (!slotGuid) {
			return false;
		}
		
		var $slotElement = $('#' + slotGuid);
		$slotElement.hide();

		ajax.action('entity/delete', {
			data: {
				guid: slotGuid
			},
			success: function(json) {
				$slotElement.remove();
			},
			error: function() {
				$slotElement.show();
			}
		});
	});
	
	$(document).on('click', '#event-manager-new-slot-set-name-button', function() {
		var set_name = $('#event-manager-new-slot-set-name').val();
		if (set_name !== '') {
			$("#event_manager_form_program_slot input[name='slot_set']").prop('checked', false);
			var $options = $("#event_manager_form_program_slot input[name='slot_set']:first").parent().parent().parent();
			$options.append("<li><label><input type='radio' checked='checked' value='" + set_name + "' name='slot_set'/>" + set_name + "</label></li>");
		}
	});
	
	$(document).on('submit', '#event_manager_form_program_slot', addSlot);
	$(document).on('submit', '#event_manager_form_program_day', addDay);
});
