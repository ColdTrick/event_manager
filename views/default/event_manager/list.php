<?php 
    
	echo "<div id='event_manager_event_listing'>";
	
	if(!empty($vars["entities"])){
		echo elgg_view_entity_list($vars["entities"], $vars["count"], $vars["offset"], 10, false, true, false);
	} else {
		echo elgg_echo('event_manager:list:noresults');
	}
	
	echo "</div>";
	