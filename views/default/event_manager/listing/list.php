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
	'no_results' => true,
];

$options = array_merge($defaults, $options);

$tag = get_input('tag');
if (!empty($tag)) {
	echo elgg_view('event_manager/listing/elements/tags');
	
	$options['metadata_name_value_pairs'][] = [
		'name' => 'tags',
		'value' => $tag,
		'case_sensitive' => false,
	];
}

echo elgg_list_entities($options);
