<?php

	/**
	 * Elgg profile icon
	 *
	 * @package ElggProfile
	 */

	// Load the Elgg framework
	require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

	// Get the owning event
	$eventGuid = get_input('eventGuid');
	$event = get_entity($eventGuid);
		
	// Get the size
	$size = strtolower(get_input('size'));
	$icon_sizes = elgg_get_config("icon_sizes");
	
	if (empty($size) || empty($icon_sizes) || !array_key_exists($size, $icon_sizes)) {
		$size = "medium";
	}
	
	// If event doesn't exist, return default icon
	if (!$event) {
		$path = elgg_view("icon/events/default/$size");
		header("Location: $path");
		exit;
	}
		
	// Try and get the icon
	$filehandler = new ElggFile();
	$filehandler->owner_guid = $event->getOwnerGUID();
	$filehandler->setFilename("events/" . $event->getGUID() . '/' . $size . ".jpg");
	
	$success = false;
	if ($filehandler->exists()) {
		if ($contents = $filehandler->grabFile()) {
			$success = true;
		}
	}
	
	if (!$success) {
		$path = elgg_view('icon/events/default/'.$size);
		header("Location: {$path}");
		exit;
	}
	
	header("Content-type: image/jpeg");
	header("Expires: " . date("r", time() + 864000));
	header("Pragma: public");
	header("Cache-Control: public");
	header("Content-Length: " . strlen($contents));
	
	echo $contents;
	exit();
