<?php
/**
 * Calendar listing of events used by resource files
 *
 * @uses $vars['options']    Additional options for elgg_list_entities()
 * @uses $vars['resource']   The calling resource
 * @uses $vars['page_owner'] Page owner during the call
 */

elgg_require_js('event_manager/calendar');

$page_owner = elgg_extract('page_owner', $vars);

$wrapper_attr = [
	'id' => 'event-manager-event-calendar-wrapper',
	'data-resource' => elgg_extract('resource', $vars),
	'data-guid' => ($page_owner instanceof ElggEntity) ? $page_owner->guid : null,
	'data-tag' => get_input('tag'),
];
$content = elgg_format_element('div', ['id' => 'event-manager-event-calendar']);

echo elgg_view('event_manager/listing/elements/tags');

echo elgg_format_element('div', $wrapper_attr, $content);
