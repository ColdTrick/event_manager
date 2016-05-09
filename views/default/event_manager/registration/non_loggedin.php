<?php

echo elgg_view_input('text', [
	'label' => elgg_echo('user:name:label'),
	'name' => 'question_name',
	'value' => $_SESSION['registerevent_values']['question_name'],
	'required' => true,
]);

echo elgg_view_input('text', [
	'label' => elgg_echo('email'),
	'name' => 'question_email',
	'value' => $_SESSION['registerevent_values']['question_email'],
	'required' => true,
]);
