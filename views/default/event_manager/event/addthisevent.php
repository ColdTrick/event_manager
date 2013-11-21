<?php

$event = elgg_extract("entity", $vars);
if (!$event) {
	return;
}

$link = "<a class='elgg-button elgg-button-action' href='" . $event->getURL() . "'>" . elgg_view_icon("calendar", "float mrs") . elgg_echo("Add to Calendar") . "</a>";

$location = $event->getLocation();
if (empty($location)) {
	$location = $event->venue;
}

$start = date(EVENT_MANAGER_FORMAT_DATE_EVENTDAY, $event->start_day) . " " . date('H', $event->start_time) . ":" . date('i', $event->start_time) . ":00";

if ($event->end_day) {
	$end = date(EVENT_MANAGER_FORMAT_DATE_EVENTDAY, $event->end_day);
} else {
	$end = date_add(date_create($start), date_interval_create_from_date_string("1 hour"));
	$end = $end->format('Y-m-d H:i:s');
}

$title = $event->title;
$description = $event->description;
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