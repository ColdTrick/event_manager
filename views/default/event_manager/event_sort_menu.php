<?php

	 $url = EVENT_MANAGER_BASEURL."/event/list/";
?>

<div id="event_manager_result_navigation">
	<div id="event_manager_result_refreshing"><?php echo elgg_echo("event_manager:list:navigation:refreshing"); ?></div>
	<div id="elgg_horizontal_tabbed_nav">
		<ul>
			<li class="selected">	
				<a href="javascript:void(0);" rel="list"><?php echo elgg_echo('event_manager:list:navigation:list'); ?></a>
			</li>
			<li>
				<a href="javascript:void(0);" rel="onthemap"><?php echo elgg_echo('event_manager:list:navigation:onthemap'); ?></a>
			</li>
		</ul>
	</div>
</div>