<?php

$guid = (int) get_input('guid');

elgg_entity_gatekeeper($guid, 'object', \ColdTrick\EventManager\Event\Day::SUBTYPE);
$entity = get_entity($entity);

if (!$entity->delete()) {
	system_message(elgg_echo('entity:delete:success'));
}