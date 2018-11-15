<?php
$guid = (int) get_input('guid');

elgg_entity_gatekeeper($guid, 'object', Event::SUBTYPE);
$entity = get_entity($guid);

$forward = 'events';
$container = $entity->getContainerEntity();
if ($container instanceof \ElggGroup) {
	$forward = 'events/event/list/' . $container->guid;
}

$title = $entity->getDisplayName();

if (!$entity->delete()) {
	return elgg_error_response(elgg_echo('entity:delete:fail', [$title]));
}

return elgg_ok_response('', elgg_echo('entity:delete:success', [$title]), $forward);
