elgg.provide('elgg.event_manager');

elgg.event_manager.edit_event_init = function() {
	$("#event-manager-forms-event-edit li").on("click", function(event, elem) {
		var href = $(this).find("> a").attr("href");

		// First make sure all tabs are hidden
		$(".event-tab").hide();

		// Now show the selected tab
		$(href).show();

		$(this).parent().find(".elgg-state-selected").removeClass("elgg-state-selected");
		$(this).addClass("elgg-state-selected");
		return false;
	});
};

elgg.register_hook_handler('init', 'system', elgg.event_manager.edit_event_init);