<?php 
	
$event = elgg_extract('entity', $vars);

if (!$event) {
	return;
}

$files = json_decode($event->files);

if (empty($files)) {
	return;
}

$rows = '';
foreach ($files as $file) {
	$link = elgg_view('output/url', [
		'href' => "/events/event/file/{$event->getGUID()}/{$file->file}",
		'text' => $file->title
	]);
	
	$delete = elgg_view('output/url', [
		'href' => "action/event_manager/event/deletefile?guid={$event->getGUID()}&file={$file->file}",
		'text' => elgg_view_icon('delete'),
		'confirm' => true
	]);
	
	$rows .= "<tr><td>$link</td><td>$delete</td></tr>";
}

$content = elgg_format_element('table', ['class' => 'elgg-table'], $rows);
	
echo elgg_view_module('info', elgg_echo('event_manager:edit:form:files'), $content, ['class' => 'mtm']);
