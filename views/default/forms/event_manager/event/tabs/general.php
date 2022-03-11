<?php
use Elgg\Values;

/**
 * Form fields for general event information
 */

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('title'),
	'name' => 'title',
	'value' => $vars['title'],
	'required' => true,
]);

echo elgg_view_field([
	'#type' => 'fieldset',
	'align' => 'horizontal',
	'fields' => [
		[
			'#type' => 'date',
			'#label' => elgg_echo('event_manager:edit:form:start'),
			'#class' => 'elgg-field-horizontal',
			'name' => 'event_start',
			'timestamp' => true,
			'required' => true,
			'value' => $vars['event_start'],
			'class' => 'event_manager_event_edit_date',
		],
		[
			'#type' => 'time',
			'name' => 'start_time',
			'value' => $vars['event_start'],
			'timestamp' => true,
		],
		[
			'#type' => 'date',
			'#label' => elgg_echo('event_manager:edit:form:end'),
			'#class' => 'elgg-field-horizontal',
			'name' => 'event_end',
			'timestamp' => true,
			'required' => true,
			'value' => $vars['event_end'],
			'class' => 'event_manager_event_edit_date',
		],
		[
			'#type' => 'time',
			'name' => 'end_time',
			'value' => $vars['event_end'],
			'timestamp' => true,
		],
	],
]);

$announcement_period = $vars['announcement_period'];
$notification_queued_ts = $vars['notification_queued_ts'];
$notification_sent_ts = $vars['notification_sent_ts'];

if (!empty($notification_sent_ts)) {
	// notification already sent
	echo elgg_echo('event_manager:edit:form:announcement_period:sent', [Values::normalizeTime($notification_sent_ts)->formatLocale(elgg_echo('friendlytime:date_format:short'))]);
	
	return;
}

if (!empty($notification_queued_ts) && $notification_queued_ts <= time()) {
	// notification scheduled in the passed
	echo elgg_echo('event_manager:edit:form:announcement_period:scheduled', [Values::normalizeTime($notification_queued_ts)->formatLocale(elgg_echo('friendlytime:date_format:short'))]);
	
	return;
}

if (!empty($vars['guid']) && empty($notification_queued_ts)) {
	// scheduled notifications not supported, probably an event created before this feature existed
	return;
}

echo elgg_view_field([
	'#type' => 'number',
	'#label' => elgg_echo('event_manager:edit:form:announcement_period'),
	'#help' => elgg_echo('event_manager:edit:form:announcement_period:help'),
	'name' => 'announcement_period',
	'value' => $announcement_period,
	'min' => 0,
]);

if (!empty($notification_queued_ts)) {
	// notification scheduled in the future
	echo elgg_echo('event_manager:edit:form:announcement_period:scheduled', [Values::normalizeTime($notification_queued_ts)->formatLocale(elgg_echo('friendlytime:date_format:short'))]);
}
