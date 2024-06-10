<?php

$event = elgg_extract('event', $vars);
$object = elgg_extract('object', $vars);
$rel = $event->getRelationshipByUser($object->guid);

if ($rel === EVENT_MANAGER_RELATION_ATTENDING) {
	$completed_text = $event->registration_completed ?: elgg_echo('event_manager:registration:completed', [$object->getDisplayName(), $event->getDisplayName()]);
	
	$completed_text = str_ireplace('[NAME]', $object->getDisplayName(), $completed_text);
	$completed_text = str_ireplace('[EVENT]', $event->getDisplayName(), $completed_text);
	
	echo elgg_view('output/longtext', ['value' => $completed_text]);
}

echo elgg_view('output/longtext', ['value' => elgg_echo('event_manager:event:relationship:message:' . $rel)]);

echo elgg_format_element('div', ['class' => 'mtm'], elgg_view('output/url', [
	'text' => elgg_echo('event_manager:registration:continue'),
	'href' => $event->getURL(),
	'class' => 'elgg-button elgg-button-action',
]));
