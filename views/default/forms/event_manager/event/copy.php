<?php

$entity = elgg_extract('entity', $vars);
if (!($entity instanceof Event)) {
	return;
}
$body = '<div class="clearfix mbm">';
$body .= elgg_view('forms/event_manager/event/tabs/general', [
	'title' => elgg_echo('event_manager:entity:copy', [$entity->getDisplayName()]),
	'event_start' => $entity->getStartTimestamp(),
	'event_end' => $entity->getEndTimestamp(),
	'announcement_period' => elgg_get_plugin_setting('announcement_period', 'event_manager'),
	'notification_queued_ts' => ELGG_ENTITIES_ANY_VALUE,
	'notification_sent_ts' => ELGG_ENTITIES_ANY_VALUE,
]);
$body .= '</div>';

$body .= elgg_view_field([
	'#type' => 'access',
	'#label' => elgg_echo('access'),
	'name' => 'access_id',
	'value' => $entity->access_id,
]);
$body .= elgg_view_field([
	'#type' => 'hidden',
	'name' => 'guid',
	'value' => $entity->guid,
]);

$body .= elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_echo('save'),
]);

echo elgg_view_module('info', elgg_echo('event_manager:menu:copy'), $body);
