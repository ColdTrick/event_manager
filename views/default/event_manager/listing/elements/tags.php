<?php

$tag = get_input('tag');
if (empty($tag)) {
	return;
}

$tag_link = elgg_view('output/url', [
	'href' => elgg_http_add_url_query_elements(current_page_url(), ['tag' => null]),
	'text' => $tag,
	'icon_alt' => 'remove',
]);

echo elgg_format_element('div', ['class' => 'event-manager-listing-tags mbm'], elgg_echo('event_manager:list:filter:tags', [$tag_link]));
