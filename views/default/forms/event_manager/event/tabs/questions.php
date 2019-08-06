<?php
$event = elgg_extract('entity', $vars);

// Have to do this for private events
echo elgg_call(ELGG_IGNORE_ACCESS, function() use ($event) {
	$results = '';
	$no_results = function() {
		echo elgg_format_element('ul', [
			'class' => 'elgg-list event_manager_registrationform_fields',
		]);
	};
	
	if ($event instanceof \Event) {
		$results .= elgg_view_entity_list($event->getRegistrationFormQuestions(), [
			'list_class' => 'event_manager_registrationform_fields',
			'item_view' => 'forms/event_manager/registrationform/question',
			'no_results' => $no_results,
		]);
	} else {
		$results .= $no_results();
	}
	
	$results .= elgg_format_element('li', [
		'id' => 'event-manager-registration-field-template',
		'class' => 'hidden elgg-item elgg-item-object elgg-item-object-eventregistrationquestion ui-sortable-handle',
	], elgg_view('forms/event_manager/registrationform/question'));
	
	$results .= elgg_view('output/url', [
		'href' => 'javascript:void(0);',
		'class' => 'elgg-button elgg-button-action event-manager-registration-add-field',
		'text' => elgg_echo('event_manager:editregistration:addfield'),
	]);
	
	return $results;
});
