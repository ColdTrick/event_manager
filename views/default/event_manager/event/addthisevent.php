<?php

$event = elgg_extract("entity", $vars);
if (!$event) {
	return;
}

$link = "<a class='elgg-button elgg-button-action' href='" . $event->getURL() . "'>" . elgg_view_icon("calendar", "float mrs") . elgg_echo("event_manager:event:menu:title:add_to_calendar") . "</a>";

$location = $event->getLocation();
if (empty($location)) {
	$location = $event->venue;
}

$start = date(EVENT_MANAGER_FORMAT_DATE_EVENTDAY, $event->start_day) . " " . date('H', $event->start_time) . ":" . date('i', $event->start_time) . ":00";

if ($event->end_ts) {
	$end = date('Y-m-d H:i:00', $event->end_ts);
} else {
	$end_ts = mktime(date('H', $event->start_time), date('i', $event->start_time), 0,date('m', $event->start_day), date('d', $event->start_day), date('Y', $event->start_day));
	$end_ts = $end_ts + 3600;
	$end = date('Y-m-d H:i:00', $end_ts);
}

$title = $event->title;
$description = elgg_get_excerpt($event->description, 500);
$organizer = $event->organizer;

?>
<span class="addthisevent">
	<?php echo $link; ?>
	<div>
		<span class="_start"><?php echo $start; ?></span>
		<span class="_end"><?php echo $end; ?></span>
		<span class="_summary"><?php echo $title; ?></span>
		<span class="_description"><?php echo $description; ?></span>
		<span class="_location"><?php echo $location; ?></span>
		<span class="_organizer"><?php echo $organizer;?></span>
		<span class="_organizer_email">noreply</span>
		<span class="_date_format">YYYY-MM-DD</span>
	</div>
</span>

<?php

/*

<span class="_start">10-05-2012 11:38:46</span>
<span class="_end">11-05-2012 11:38:46</span>
<span class="_zonecode">35</span>
<span class="_summary">Summary of the event</span>
<span class="_description">Description of the event</span>
<span class="_location">Location of the event</span>
<span class="_organizer">Organizer</span>
<span class="_organizer_email">Organizer e-mail</span>
<span class="_facebook_event">http://www.facebook.com/events/160427380695693</span>
<span class="_all_day_event">true</span>
<span class="_date_format">DD/MM/YYYY</span>

 */