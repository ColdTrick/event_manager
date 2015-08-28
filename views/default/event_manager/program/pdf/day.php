<?php

$day = $vars["entity"];
$participate = $vars['participate'];
$register_type = $vars['register_type'];

if (!empty($day) && ($day instanceof EventDay)) {
	
	$slots = "";
	
	if ($daySlots = $day->getEventSlots()) {
		foreach ($daySlots as $slot) {
			$slots .= elgg_view("event_manager/program/pdf/slot", array(
				'entity' => $slot, 
				'participate' => $participate, 
				'register_type' => $register_type, 
				'user_guid' => $vars['user_guid']
			));
		}
	}
	
	if (!empty($slots)) {
		
		$title = event_manager_format_date($day->date);
		
		if ($description = $day->description) {
			$title = $description . " (" . $title . ")";
		}
		
		$result = "<div>{$title}</div>";
		if ($day->title) {
			$result .= "<div>{$day->title}</div>";
		}
		
		$result .= "<br /><br />{$slots}<br /><br />";
		
		echo $result;
	}
}
