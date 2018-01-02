<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof Event) {
	return;
}

$location = $entity->location;
if (empty($location)) {
	return;
}

echo elgg_format_element('div', [
	'id' => 'event-manager-gmaps-location',
	'class' => 'event-manager-event-view-maps',
]);

?>
<script>
	require(['event_manager/maps'], function (EventMap) {
		EventMap.setup('#event-manager-gmaps-location', '<?php echo $location; ?>');
	});
</script>
