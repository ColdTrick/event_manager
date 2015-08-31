<?php 

$guid = get_input('guid');
$filename = get_input('file');

if (empty($guid) || empty($filename)) {
	register_error(elgg_echo('event_manager:event:file:notfound:text'));
	forward(REFERER);	
}
	
$event = get_entity($guid);
if (empty($event) || ($event->getSubtype() !== Event::SUBTYPE)) {
	register_error(elgg_echo('event_manager:event:file:notfound:text'));
	forward(REFERER);
}

$files = json_decode($event->files);
foreach ($files as $file) {
	if (strtolower($file->file) !== strtolower($filename)) {
		continue;	
	}
	
	$prefix = "events/" . $event->getGUID() . "/files/";
		
	$fileHandler = new ElggFile();
	$fileHandler->setFilename($prefix . $file->file);
	
	$fileHandler->owner_guid = $event->owner_guid;
	
	//fix for IE https issue
	header('Pragma: public');
	header('Content-Type: ' . $file->mime);
	header('Content-Disposition: Attachment; filename=' . $file->file);				
	
	echo $fileHandler->grabFile();
	exit;
}

