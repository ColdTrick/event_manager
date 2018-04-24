define(function(require) {
	
	var $ = require('jquery');
	var Ajax = require('elgg/Ajax');
	
	$(document).on('submit', 'form.elgg-form-event-manager-event-attendees', function() {
		
		var $form = $(this);
		var ajax = new Ajax();
		ajax.view('event_manager/event/attendees_list', {
			data: ajax.objectify(this),
			success: function(data) {
				console.log(data);
				
				$form.nextAll('ul.elgg-list, ul.elgg-pagination, p.elgg-no-results').remove();
				$form.after(data);
			}
		});
		
		return false;
	});
});
