elgg.provide('elgg.event_manager');

elgg.event_manager.execute_search = function(event) {
	
	event.preventDefault();

	if($('#event-manager-search-form-past-events').is(':hidden')) {
		$('#advanced_search').val('1');
	} else {
		$('#advanced_search').val('0');
	}
	
	if ($(".elgg-menu-events-list li.elgg-state-selected a").attr("rel") == "onthemap") {
		elgg.trigger_hook('search:onthemap', 'event_manager');
	} else {
		elgg.event_manager.execute_search_list();
	}
};

elgg.event_manager.execute_search_list = function() {
	require(['elgg/spinner'], function(spinner) {
		spinner.start();

		elgg.action('event_manager/event/search', {
			data: $('#event_manager_search_form').serialize(),
			success: function(data) {
				var response = data.output;
				
				$('#event_manager_event_list_search_more').remove();
				$('#event_manager_event_listing').html(response.content);
			},
			complete: function() {
				spinner.stop();
			}
		});
	});
};

elgg.event_manager.list_events_init = function() {
	require(['elgg/spinner']);

	$(document).on('click', '#event_manager_event_list_search_more', function()	{
		var clickedElement = $(this);
		clickedElement.html('<div class="elgg-ajax-loader"></div>');
		var offset = parseInt($(this).attr('rel'), 10);
		
		require(['elgg/spinner'], function(spinner) {
			spinner.start();
			
			var formData = '';
			
			if ($('#event-manager-search-form-past-events').is(":hidden") === true) {
				formData = $("#event_manager_search_form").serialize();
			} else {
				formData = $($("#event_manager_search_form")[0].elements).not($("#event_manager_event_search_advanced_container")[0].children).serialize();
			}
	
			elgg.action('event_manager/event/search?offset=' + offset, {
				data: $('#event_manager_search_form').serialize(),
				success: function(data) {
					if (data.output) {
						$('#event_manager_event_list_search_more').remove();
						$('#event_manager_event_listing').append(data.output.content);
					}	
				},
				complete: function() {
					spinner.stop();
				}
			});
		});
	});

	$(document).on('submit', '#event_manager_search_form', elgg.event_manager.execute_search);

	$('.elgg-menu-events-list li a[rel]').click(function(e) {
		e.preventDefault();
		
		var $clicked_item = $(this).parent();
		var $menu_items = $('.elgg-menu-events-list li');
		var selected = $(this).attr('rel');
		
		if ($clicked_item.hasClass('elgg-state-selected')) {
			return;
		}
		
		$menu_items.removeClass('elgg-state-selected');
		$clicked_item.addClass('elgg-state-selected');

		$('.event-manager-results, #event_manager_search_form').hide();
		if (selected !== 'calendar') {
			$('#event_manager_search_form').show();
		}

		$('#search_type').val(selected);

		if (selected == 'onthemap') {
			$('#event_manager_event_map').show();
			elgg.trigger_hook('tab:onthemap', 'event_manager');
			
		} else if (selected == 'list') {
			$('#event_manager_event_listing').show();
			elgg.event_manager.execute_search_list();
		} else if (selected == 'calendar') {
			$('#event-manager-event-calendar').show();
			if (false) {
				return;
			}
			require(['elgg/spinner'], function(spinner) {
				spinner.start();
				
				require(['event_manager/calendar'], function () {
					spinner.stop();
				});
			});
		}
	});
};

elgg.register_hook_handler('init', 'system', elgg.event_manager.list_events_init);