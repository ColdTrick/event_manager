<?php
$guid = (int) get_input('guid');

elgg_entity_gatekeeper($guid, 'object', Event::SUBTYPE);
$entity = get_entity($guid);

$forward = 'events';
$container = $entity->getContainerEntity();
if ($container instanceof \ElggGroup) {
	$forward = 'events/event/list/' . $container->getGUID();
}
$title = $entity->title;
if ($entity->delete()) {
	system_message(elgg_echo('entity:delete:success', [$title]));
	forward($forward);
} else {
	register_error(elgg_echo('entity:delete:fail', [$title]));
}

forward(REFERER);
