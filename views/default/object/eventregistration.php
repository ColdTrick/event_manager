<?php
/**
 * Show an event registration object. This is a non-registered site user who registered to an event
 *
 * @uses $vars['entity'] EventRegistration the non-registered user
 */

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof EventRegistration) {
	return;
}

$params = [
	'title' => $entity->getDisplayName(),
	'icon_entity' => $entity,
	'subtitle' => false,
];
$params = $params + $vars;
echo elgg_view('object/elements/summary', $params);
