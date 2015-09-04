elgg.provide('elgg.event_manager');

elgg.event_manager.view_event_init = function() {
	$(document).on('click', '#event_manager_event_view_program a', function() {
		$('.event_manager_program_day').hide();
		$('#event_manager_event_view_program li').removeClass('elgg-state-selected');
		var selected = $(this).attr('rel');
		$(this).parent().addClass('elgg-state-selected');
		$('#' + selected).show();
	});
};

elgg.register_hook_handler('init', 'system', elgg.event_manager.view_event_init);