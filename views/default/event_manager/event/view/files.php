<?php

$event = elgg_extract('entity', $vars);
if (!$event instanceof \Event) {
	return;
}

$event_files = elgg_view_menu('event_files', ['entity' => $event]);
$can_edit = $event->canEdit();
if (empty($event_files) && !$can_edit) {
	return;
}

$module_vars = [];
if ($can_edit) {
	$module_vars['menu'] = elgg_view('output/url', [
		'href' => elgg_generate_url('edit:object:event:upload', ['guid' => $event->guid]),
		'title' => elgg_echo('event_manager:event:uploadfiles'),
		'text' => elgg_echo('upload'),
		'icon' => 'plus-circle',
	]);
}

if (empty($event_files)) {
	$event_files = elgg_echo('event_manager:event:uploadfiles:no_files');
}

echo elgg_view_module('info', elgg_echo('event_manager:edit:form:files'), $event_files, $module_vars);
