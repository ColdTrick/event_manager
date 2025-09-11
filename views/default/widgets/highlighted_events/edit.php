<?php

/* @var $widget \ElggWidget */
$widget = elgg_extract('entity', $vars);

echo elgg_view_field([
	'#type' => 'objectpicker',
	'#label' => elgg_echo('widgets:highlighted_events:edit:event_guids'),
	'#help' => elgg_echo('widgets:highlighted_events:description'),
	'values' => $widget->event_guids,
	'name' => 'params[event_guids]',
	'subtype' => Event::SUBTYPE,
	'sortable' => true,
]);

echo elgg_view_field([
	'#type' => 'switch',
	'#label' => elgg_echo('widgets:highlighted_events:edit:show_past_events'),
	'name' => 'params[show_past_events]',
	'value' => $widget->show_past_events,
]);
?>
<script>
	$(document).ready(function() {
		$('#widget-edit-<?php echo $widget->guid; ?> ul.elgg-entity-picker-list').sortable();
	});
</script>
