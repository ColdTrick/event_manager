<?php

/**
 * List upcoming events on group profile page
 */

if ($vars['entity']->event_manager_enable != 'no') {
?>

<div class="group_widget">
<h2><?php echo elgg_echo('event_manager:group'); ?></h2>
<?php
	$current_context = get_context();
	set_context('widget');

	$event_options = array();
	if(($page_owner = page_owner_entity())){
		$event_options["container_guid"] = $page_owner->getGUID();
	}
	
	$events = event_manager_search_events($event_options);
	$content = elgg_view_entity_list($events['entities'], 0, 0, 5, false);	
	
	set_context($current_context);
	
    if ($content) {
		echo $content;

		$more_url = EVENT_MANAGER_BASEURL."/event/list/{$vars['entity']->username}";
		echo "<div class=\"forum_latest\"><a href=\"$more_url\">" . elgg_echo('event_manager:group:more') . "</a></div>";
	} else {
		echo "<div class=\"forum_latest\">" . elgg_echo("event_manager:nogroup") . "</div>";
	}
	
?>
</div>
<?php
}