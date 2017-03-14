<?php

$event = elgg_extract('entity', $vars);
if (!$event) {
	return;
}

$link = elgg_view('output/url', [
	'class' => 'elgg-button elgg-button-action',
	'href' => $event->getURL(),
	'text' => elgg_view_icon('calendar', 'float mrs') . elgg_echo('event_manager:event:menu:title:add_to_calendar'),
]);

$location = $event->location ?: $event->venue;

$start = $event->getStartDate('d/m/Y H:i:00');
$end = $event->getEndDate('d/m/Y H:i:00');

$title = $event->title;

$description = '';
if (!empty($event->location)) {
	// add venue to description
	$description .= $event->venue . PHP_EOL;
}

$description .= $event->getExcerpt(100000);

?>
<span class="addthisevent">
	<?php echo $link; ?>
	<div class='hidden'>
		<span class="start"><?php echo $start; ?></span>
		<span class="end"><?php echo $end; ?></span>
		<span class="title"><?php echo $title; ?></span>
		<span class="description"><?php echo $description; ?></span>
		<span class="location"><?php echo $location; ?></span>
		<span class="date_format">DD/MM/YYYY</span>
	</div>
</span>
