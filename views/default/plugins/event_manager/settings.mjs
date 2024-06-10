import 'jquery';

$(document).on('change', 'select[name="params[maps_provider]"]', function() {
	var selected_value = $(this).val();
	
	$('.event-manager-maps-provider').hide();
	$('.event-manager-maps-provider-' + selected_value).show();
});
