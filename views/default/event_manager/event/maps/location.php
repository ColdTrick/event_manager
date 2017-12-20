<?php
$event = elgg_extract('entity', $vars);

$maps_provider = elgg_get_plugin_setting('maps_provider', 'event_manager', 'google');
if ($maps_provider === 'none') {
	return;
}

echo elgg_format_element('div', [
	'id' => 'event-manager-gmaps-location',
	'class' => 'event-manager-gmaps-location',
]);

?>
<script>
	require(['event_manager/maps'], function (EventMap) {
		EventMap.setup('#event-manager-gmaps-location', '<?php echo $event->location?>');
	});
</script>