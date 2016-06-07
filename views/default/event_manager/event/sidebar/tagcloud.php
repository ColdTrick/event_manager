<?php

$entity = elgg_extract('entity', $vars);
if (!($entity instanceof Event)) {
	return;
}

$tags = $entity->tags;
if (empty($tags)) {
	return;
}

if (!is_array($tags)) {
	$tags = (array) $tags;
}

if (elgg_view_exists('output/tagcloud')) {
	$tag_data = [];
	foreach ($tags as $tag) {
		$tag_info = new stdClass();
		$tag_info->tag = $tag;
		$tag_info->total = 1;
		
		$tag_data[] = $tag_info;
	}
	
	$tagcloud = elgg_view('output/tagcloud', [
		'value' => $tag_data,
		'type' => 'object',
		'subtype' => 'event',
	]);
} else {
	$tagcloud = elgg_view('output/tags', ['value' => $tags]);
}

echo elgg_view_module('aside', elgg_echo('tagcloud'), $tagcloud);