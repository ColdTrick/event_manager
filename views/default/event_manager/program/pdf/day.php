<?php 

	$day = $vars["entity"];
	$participate = $vars['participate'];
	$register_type = $vars['register_type'];
	
	if (!empty($day) && ($day instanceof EventDay)) {
		
		$slots = "";
		
		if ($daySlots = $day->getEventSlots()) {
			foreach ($daySlots as $slot) {
				$slots .= elgg_view("event_manager/program/pdf/slot", array("entity" => $slot, 'participate' => $participate, 'register_type' => $register_type));							
			}
		}
		
		if (!empty($slots)){
			
			$result = '<div>' . $day->title .' ('.date(EVENT_MANAGER_FORMAT_DATE_EVENTDAY, $day->date).')</div><br /><br />';
			
			$result .= $slots;
			
			echo $result;
		}
	}