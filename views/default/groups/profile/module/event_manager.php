<?php
/**
 * Group event manager module
 */

$params = [
	'title' => elgg_echo('event_manager:group'),
	'entity_type' => 'object',
	'entity_subtype' => 'event',
];
$params = $params + $vars;

echo elgg_view('groups/profile/module', $params);
