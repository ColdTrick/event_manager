<?php ?>
import 'jquery';
import 'elgg';
import lightbox from 'elgg/lightbox';

<?php echo elgg_view('fullcalendar.js'); ?>

var calendarEl = document.getElementById('event-manager-event-calendar');
var wrapper_data = $('#event-manager-event-calendar-wrapper').data();
var events_url = 'event_manager/calendar?' + $.param(wrapper_data);

var calendar = new FullCalendar.Calendar(calendarEl, {
	events: elgg.normalize_url(events_url),
	headerToolbar: {
		left: 'prev next today',
		center: 'title',
		right: 'dayGridMonth timeGridWeek timeGridDay'
	},
	views: {
		dayGridMonth: {
			dayMaxEventRows: 5
		}
	},
	eventTimeFormat: { // like '14:30'
		hour: 'numeric',
		minute: '2-digit',
		meridiem: false
	},
	defaultAllDay: true,
	locale: elgg.config.current_language,
	timeZone: 'UTC',
	eventClick: function (info) {
		var guid = info.event.extendedProps.guid;
		if (!guid) {
			return;
		}
		
		lightbox.open({
			'href': elgg.normalize_url('ajax/view/event_manager/event/popup?guid=' + guid),
		});
		
		info.jsEvent.preventDefault(); // don't let the browser navigate
	}
});

calendar.render();
