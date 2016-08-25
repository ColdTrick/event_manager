define(['jquery', 'elgg', 'fullcalendar'], function($, elgg) {

	var init = function() {
		$('#event-manager-event-calendar').fullCalendar({
			events: elgg.normalize_url('ajax/view/event_manager/calendar?container_guid=' + elgg.get_page_owner_guid()),
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			allDayDefault: true,
			timeFormat: 'H:mm',
			lang: elgg.get_language(),
			buttonText: {
				today: elgg.echo('event_manager:calendar:today'),
				month: elgg.echo('event_manager:calendar:month'),
				week: elgg.echo('event_manager:calendar:week'),
				day: elgg.echo('event_manager:calendar:day')
			}
		});
	};

	elgg.register_hook_handler('init', 'system', init);
});