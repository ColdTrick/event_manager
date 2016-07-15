<?php

$title = elgg_extract('title', $vars);
$body = elgg_extract('body', $vars);

$module_vars = [
	'id' => elgg_extract('id', $vars),
	'class' => 'event_tab',
];

echo elgg_view_module('info', $title, $body, $module_vars);