<?php 
    
	echo "<div id='event_manager_event_listing'>";
	
	$options = array(
		"count" => $vars["count"],
		"offset" => $vars["offset"],
		"full_view" => false,
		"pagination" => false
		);
	
	echo elgg_view_entity_list($vars["entities"], $options);
	
	echo "</div>";
