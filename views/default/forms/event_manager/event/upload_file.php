<?php

$event = elgg_extract('entity', $vars);
if (empty($event)) {
	return;
}

echo elgg_view('input/hidden', [
	'name' => 'guid', 
	'value' => $event->getGUID()
]);
echo '<div><label>' . elgg_echo('title') . ' *</label>';
echo elgg_view('input/text', [
	'name' => 'title', 
	'required' => true
]) . '</div>';
echo '<div><label>' . elgg_echo('event_manager:edit:form:file') . ' *</label>';
echo elgg_view('input/file', ['name' => 'file', 'required' => true]);
echo '</div>';
echo elgg_view('input/submit', ['value' => elgg_echo('upload')]);
echo elgg_format_element('div', ['class' => 'elgg-subtext'], '(* = ' . elgg_echo('requiredfields') . ')');
