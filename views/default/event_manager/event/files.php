<?php
	
$event = elgg_extract('entity', $vars);
if (!$event instanceof \Event) {
	return;
}

$files = $event->getFiles();
if (empty($files)) {
	return;
}

$rows = '';
$elggfile = new \ElggFile();
$elggfile->owner_guid = $event->guid;

$use_cookie = ($event->access_id !== ACCESS_PUBLIC);

foreach ($files as $file) {
	$elggfile->setFilename($file->file);
	
	if (!$elggfile->exists()) {
		// check old storage location
		$elggfile->setFilename("files/{$file->file}");
	}
		
	$link = elgg_view_url(elgg_get_download_url($elggfile, $use_cookie), $file->title);
	
	$delete = elgg_view('output/url', [
		'href' => elgg_generate_action_url('event_manager/event/deletefile', [
			'guid' => $event->guid,
			'file' => $file->file,
		]),
		'icon' => 'delete',
		'text' => elgg_echo('delete'),
		'confirm' => true,
	]);
	
	$rows .= "<tr><td>$link</td><td>$delete</td></tr>";
}

$content = elgg_format_element('table', ['class' => 'elgg-table'], $rows);
	
echo elgg_view_module('info', elgg_echo('event_manager:edit:form:files'), $content, ['class' => 'mtm']);
