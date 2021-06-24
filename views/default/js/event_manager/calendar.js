define(['jquery', 'elgg', 'elgg/Ajax', 'elgg/lightbox', 'fullcalendar'], function($, elgg, Ajax, lightbox) {
	$('#event-manager-event-calendar').fullCalendar({
		events: function(start, end, timezone, callback) {
			var ajax = new Ajax();
			var wrapper_data = $('#event-manager-event-calendar-wrapper').data();
			
			var events = ajax.view('event_manager/calendar', {
				data: {
					view: 'json',
					start: start.toString(),
					end: end.toString(),
					...wrapper_data
				},
				success: function(result) {
					callback(result);
				}
			});
		},
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
		},
		eventClick: function(info) {
			if (!info.guid) {
				return;
			}
			
			lightbox.open({
				'href': elgg.normalize_url('ajax/view/event_manager/event/popup?guid=' + info.guid),
			});
			
			return false;
		}
	});
});
