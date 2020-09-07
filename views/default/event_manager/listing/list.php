<?php
/**
 * Default Elgg listing of events used by resource files
 *
 * @uses $vars['options'] Additional options for elgg_list_entities()
 */

$options = (array) elgg_extract('options', $vars, []);

$defaults = [
	'type' => 'object',
	'subtype' => \Event::SUBTYPE,
	'no_results' => elgg_echo('event_manager:list:noresults'),
];

$options = array_merge($defaults, $options);

echo elgg_list_entities($options);
