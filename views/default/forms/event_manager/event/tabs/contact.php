<?php

echo elgg_view_input('text', [
	'label' => elgg_echo('event_manager:edit:form:organizer'),
	'name' => 'organizer',
	'value' => $vars['organizer'],
]);

echo elgg_view_input('text', [
	'label' => elgg_echo('event_manager:edit:form:contact_details'),
	'name' => 'contact_details',
	'value' => $vars['contact_details'],
]);

echo elgg_view_input('url', [
	'label' => elgg_echo('event_manager:edit:form:website'),
	'name' => 'website',
	'value' => $vars['website'],
]);
