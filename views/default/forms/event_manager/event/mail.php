<?php
/**
 * Mail event attendees form
 */

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof \Event) {
	return;
}

echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'guid',
	'value' => $entity->guid,
]);

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('email:subject'),
	'name' => 'title',
	'value' => elgg_extract('title', $vars),
	'required' => true,
]);

echo elgg_view_field([
	'#type' => 'plaintext',
	'#label' => elgg_echo('email:body'),
	'name' => 'description',
	'value' => elgg_extract('description', $vars),
	'required' => true,
]);

$recipient_options = $entity->getSupportedRelationships();
unset($recipient_options[EVENT_MANAGER_RELATION_ATTENDING_PENDING]);
foreach ($recipient_options as $rel => $text) {
	$count = $entity->getEntitiesFromRelationship([
		'count' => true,
		'relationship' => $rel,
	]);
	
	$recipient_options[$rel] = "{$text} ({$count})";
}

$contacts = $entity->getContacts(['count' => true]);
if (!empty($contacts)) {
	$recipient_options['contacts'] = elgg_echo('event_manager:event:view:contact_persons') . " ({$contacts})";
}

$organizers = $entity->getOrganizers(['count' => true]);
if (!empty($organizers)) {
	$recipient_options[EVENT_MANAGER_RELATION_ORGANIZING] = elgg_echo('event_manager:event:relationship:event_organizing:label') . " ({$organizers})";
}

echo elgg_view_field([
	'#type' => 'checkboxes',
	'#label' => elgg_echo('event_manager:mail:recipients'),
	'name' => 'recipients',
	'options_values' => $recipient_options,
	'value' => elgg_extract('recipients', $vars),
]);

// footer
$footer = elgg_view_field([
	'#type' => 'submit',
	'text' => elgg_echo('send'),
]);

elgg_set_form_footer($footer);
