<?php

$event = elgg_extract('entity', $vars);
if (!$event) {
	return;
}

$location = $event->location ?: $event->venue;

$start = $event->getStartDate('d/m/Y H:i:00');
$end = $event->getEndDate('d/m/Y H:i:00');

$title = $event->getDisplayName();

$description = '';
if (!empty($event->location)) {
	// add venue to description
	$description .= $event->venue . PHP_EOL;
}

// removing HTML and shorter because of URL length limitations
$description .= $event->getExcerpt(500) . PHP_EOL . PHP_EOL;
$description .= $event->getURL();

?>
<span class="addthisevent">
	<?php echo elgg_echo('event_manager:event:menu:title:add_to_calendar'); ?>
	<div class='hidden'>
		<span class="start"><?php echo $start; ?></span>
		<span class="end"><?php echo $end; ?></span>
		<span class="title"><?php echo $title; ?></span>
		<span class="location"><?php echo $location; ?></span>
		<span class="date_format">DD/MM/YYYY</span>
		<span class="description"><?php echo $description; ?></span>
	</div>
</span>
