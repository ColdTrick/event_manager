define(['jquery', 'elgg', 'elgg/i18n', 'elgg/Ajax', 'elgg/lightbox', 'fullcalendar'], function($, elgg, i18n, Ajax, lightbox) {
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
		axisFormat: i18n.echo('event_manager:calendar:axis_format'),
		columnFormat: i18n.echo('event_manager:calendar:column_format'),
		lang: elgg.config.current_language,
		buttonText: {
			today: i18n.echo('event_manager:calendar:today'),
			month: i18n.echo('event_manager:calendar:month'),
			week: i18n.echo('event_manager:calendar:week'),
			day: i18n.echo('event_manager:calendar:day')
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
