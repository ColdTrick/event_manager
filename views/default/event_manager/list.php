<?php 
    
    echo "<div id='event_manager_event_listing'>";
        echo elgg_view_entity_list($vars["entities"], $vars["count"], $vars["offset"], 10, false, true, false);
    echo "</div>";
