<?php

$event = elgg_extract('entity', $vars);
if (!$event instanceof \Event) {
	return;
}

$description = $event->description ?: $event->shortdescription;
if (empty($description)) {
	return;
}

echo elgg_view_module('event', '', elgg_view('output/longtext', ['value' => $description, 'class' => 'man']));
