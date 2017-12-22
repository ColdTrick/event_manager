<?php

$guid = (int) get_input('guid');

elgg_entity_gatekeeper($guid, 'object', \ColdTrick\EventManager\Event\Day::SUBTYPE);
$entity = get_entity($guid);

$title = $entity->title;
if (!$entity->delete()) {
	return elgg_error_response(elgg_echo('entity:delete:fail', [$title]));
}

return elgg_ok_response();
