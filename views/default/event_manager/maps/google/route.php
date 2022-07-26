<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof Event) {
	return;
}

$location = $entity->location;
if (empty($location)) {
	return;
}

echo elgg_view('output/url', [
	'href' => '//maps.google.com/maps?f=d&source=s_d&daddr=' . $location . '&hl=' . elgg_get_current_language(),
	'text' => elgg_echo('event_manager:event:location:plan_route'),
	'target' => '_blank',
	'class' => 'mlm',
]);
