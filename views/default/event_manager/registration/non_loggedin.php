<?php

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('user:name:label'),
	'name' => 'question_name',
	'value' => elgg_get_sticky_value('event_register', 'question_name'),
	'required' => true,
]);

echo elgg_view_field([
	'#type' => 'email',
	'#label' => elgg_echo('email'),
	'name' => 'question_email',
	'value' => elgg_get_sticky_value('event_register', 'question_email'),
	'required' => true,
]);
