<?php

	$event = $vars["entity"];
	
	if (!empty($event) && ($event instanceof Event)) {
		if ($event->with_program) {
			$days = "";
			
			if($eventDays = $event->getEventDays()) {
				foreach ($eventDays as $key => $day) {
					$days .= elgg_view("event_manager/program/pdf/day", array("entity" => $day, "selected" => $selected, 'user_guid' => $vars['user_guid']));
				}
			}
			
			if(!empty($days)){
				$content = "<h3>" . elgg_echo('event_manager:event:program') . "</h3>";
				$content .= $days;
				
				echo $content;
			}
		}
	}