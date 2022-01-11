<?php
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
