<?php
/**
 * Default entity delete action
 */

$guid = (int) get_input('guid');
$entity = elgg_call(ELGG_IGNORE_ACCESS | ELGG_SHOW_DISABLED_ENTITIES | ELGG_SHOW_DELETED_ENTITIES, function() use ($guid) {
	return get_entity($guid);
});
if (!$entity instanceof \ColdTrick\EventManager\Event\Day && !$entity instanceof \ColdTrick\EventManager\Event\Slot) {
	return elgg_error_response(elgg_echo('entity:delete:item_not_found'));
}

if (!$entity->canDelete()) {
	return elgg_error_response(elgg_echo('entity:delete:permission_denied'));
}

// determine what name to show on success
$display_name = $entity->getDisplayName() ?: elgg_echo('entity:delete:item');

$type = $entity->getType();
$subtype = $entity->getSubtype();

if (!$entity->delete(true, true)) {
	return elgg_error_response(elgg_echo('entity:delete:fail', [$display_name]));
}

$success_keys = [
	"entity:delete:{$type}:{$subtype}:success",
	"entity:delete:{$type}:success",
	'entity:delete:success',
];

$message = '';
if (get_input('show_success', true)) {
	foreach ($success_keys as $success_key) {
		if (elgg_language_key_exists($success_key)) {
			$message = elgg_echo($success_key, [$display_name]);
			break;
		}
	}
}

return elgg_ok_response('', $message);
