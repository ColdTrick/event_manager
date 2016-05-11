<?php
$guid = (int) get_input('guid');
$rel = get_input('rel', EVENT_MANAGER_RELATION_ATTENDING);

elgg_entity_gatekeeper($guid, 'object', Event::SUBTYPE);
$event = get_entity($guid);

if (!$event->canEdit()) {
	register_error(elgg_echo('actionunauthorized'));
	forward(REFERER);
}

header('Content-Type: text/csv');
header('Content-Disposition: Attachment; filename=export.csv');
header('Pragma: public');

echo event_manager_export_attendees($event, $rel);
exit;
