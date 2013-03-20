<?php

if($event = elgg_extract("entity", $vars)){
	if($hash = $event->twitter_hash){
		$query = urlencode($hash);
		if(!empty($query)){
			elgg_load_js("jquery.tweet");
	
			$body =
<<<TWITTER_FEED
	<div id="event-manager-event-twitter-feed"></div>
	<script type='text/javascript'>
		jQuery(function($){
		    $("#event-manager-event-twitter-feed").tweet({
		      avatar_size: 30,
		      count: 3,
		      query: "$query",
		      template: "<div class='mbl clearfix'><span class='float mrm'>{avatar}</span><div>{time}</div>{text}</div>",
		      refresh_interval: 30
		    });
		  });
	</script>
TWITTER_FEED;
			echo elgg_view_module("aside", "", $body);
		}
	}
}