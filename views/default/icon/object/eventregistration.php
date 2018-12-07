<?php
/**
 * Event registration user icon
 */

$entity = elgg_extract('entity', $vars);
$size = elgg_extract('size', $vars, 'medium');

if (!$entity instanceof EventRegistration) {
	return;
}

$icon_sizes = elgg_get_icon_sizes('user');
if (!array_key_exists($size, $icon_sizes)) {
	$size = 'medium';
}

$event = $entity->getOwnerEntity();

$name = htmlspecialchars($entity->getDisplayName(), ENT_QUOTES, 'UTF-8', false);
$username = $entity->username;

$wrapper_class = [
	'elgg-avatar',
	"elgg-avatar-$size",
];
$wrapper_class = elgg_extract_class($vars, $wrapper_class);

$icon = elgg_view('output/img', [
	'src' => $entity->getIconURL($size),
	'alt' => $name,
	'title' => $name,
	'class' => elgg_extract_class($vars, [], 'img_class'),
]);

$content = elgg_format_element('a', [], $icon);

echo elgg_format_element('div', ['class' => $wrapper_class], $content);
