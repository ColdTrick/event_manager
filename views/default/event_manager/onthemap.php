<?php

$maps_provider = elgg_get_plugin_setting('maps_provider', 'event_manager', 'google');
if ($maps_provider === 'none') {
	return;
}

?>
<div id="event_manager_event_map" class="hidden event-manager-results">
	<div id="event_manager_onthemap_canvas"></div>
	<div id="event_manager_onthemap_legend">
		<img src="//maps.google.com/mapfiles/ms/icons/yellow-dot.png" /><?php echo elgg_echo("event_manager:list:navigation:your"); ?>
		<img src="//maps.google.com/mapfiles/ms/icons/blue-dot.png" /><?php echo elgg_echo("event_manager:list:navigation:attending"); ?>
		<img src="//maps.google.com/mapfiles/ms/icons/red-dot.png" /> <?php echo elgg_echo("event_manager:list:navigation:other"); ?>
	</div>
</div>