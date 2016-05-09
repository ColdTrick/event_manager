<?php

$entity = elgg_extract('entity', $vars);
$email = elgg_get_sticky_value('event_unsubscribe', 'email', get_input('e'));

echo elgg_view('output/longtext', ['value' => elgg_echo('event_manager:unsubscribe:description', [$entity->title])]);

echo elgg_view_input('email', [
	'label' => elgg_echo('email'),
	'name' => 'email',
	'value' => $email,
	'id' => 'event-manager-unsubscribe-email',
]);

echo "<div class='elgg-foot'>";
echo elgg_view('input/hidden', ['name' => 'guid', 'value' => $entity->getGUID()]);
echo elgg_view('input/submit', ['value' => elgg_echo('submit')]);
echo "</div>";

// cleanup sticky form data
elgg_clear_sticky_form('event_unsubscribe');
