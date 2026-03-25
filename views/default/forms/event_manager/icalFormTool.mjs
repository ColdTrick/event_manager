import 'jquery';

function toggle($type) {
	const $calendar_object = $('select[name="calendar_type"]')
	const $form = $calendar_object.closest('form');
	if (!$form.length) {
		return;
	}

	if ($calendar_object[0].value === $type) {
		$(`[name="${$type}"]`, $form).each(function() {
			$(this).prop('required', true);
			$(this).closest('.elgg-field').removeClass('hidden');
		});

	} else {
		$(`[name="${$type}"]`, $form).each(function() {
			$(this).prop('required', false);
			$(this).closest('.elgg-field').addClass('hidden');
		});
	}
}

$(document).on('change', 'select[name="calendar_type"]', function() {
	toggle('owner');
	toggle('group');
});

