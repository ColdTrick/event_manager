import 'jquery';

var calendar_type_selector = 'select[name="calendar_type"]';

/**
 * Toggle owner input visibility based on the value
 * of the calendar type selector
 *
 * @param {jQuery} $calendar_type Select object
 * @returns {void}
 */
function toggleOwner($calendar_type) {
	var $form = $calendar_type.closest('form');
	if (!$form.length) {
		return;
	}
	
	if ($calendar_type[0].value === "owner") {
		$('[name="owner"]', $form).each(function() {
			$(this).prop('required', true);
			$(this).closest('.elgg-field').removeClass('hidden');
		});

	} else {
		$('[name="owner"]', $form).each(function() {
			$(this).prop('required', false);
			$(this).closest('.elgg-field').addClass('hidden');
		});
	}
}

$(document).on('change', calendar_type_selector, function() {
	toggleOwner($(this));
});

toggleOwner($(calendar_type_selector));


/**
 * Toggle group input visibility based on the value
 * of the calendar type selector
 *
 * @param {jQuery} $calendar_type Select object
 * @returns {void}
 */
function toggleGroup($calendar_type) {
	var $form = $calendar_type.closest('form');
	if (!$form.length) {
		return;
	}

	if ($calendar_type[0].value === "group") {
		$('[name="group"]', $form).each(function() {
			$(this).prop('required', true);
			$(this).closest('.elgg-field').removeClass('hidden');
		});

	} else {
		$('[name="group"]', $form).each(function() {
			$(this).prop('required', false);
			$(this).closest('.elgg-field').addClass('hidden');
		});
	}
}

$(document).on('change', calendar_type_selector, function() {
	toggleGroup($(this));
});

toggleGroup($(calendar_type_selector));
