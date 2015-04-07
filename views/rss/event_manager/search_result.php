<?php 

	echo elgg_view_entity_list($vars["entities"], array("count" => $vars["count"], "offset" => $vars["offset"], "limit" => 10, "full_view" => false));