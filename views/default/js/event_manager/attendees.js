define(function(require) {
	
	var $ = require('jquery');
	var Ajax = require('elgg/Ajax');
	
	$(document).on('submit', 'form.elgg-form-event-manager-event-attendees', function() {
		
		var form = this;
		var $form = $(form);
		var ajax = new Ajax();
		
		ajax.view('event_manager/event/attendees_list', {
			method: 'POST',
			data: ajax.objectify(form),
			success: function(data) {
				$form.nextAll('ul.elgg-list, ul.elgg-pagination, p.elgg-no-results, div.elgg-list-container').remove();
				$form.after(data);
			}
		});
		
		return false;
	});
});
