<?php
/**
 * Map listing of events used by resource files
 *
 * @uses $vars['options']    Additional options for elgg_list_entities()
 * @uses $vars['resource']   The calling resource
 * @uses $vars['page_owner'] Page owner during the call
 */

use Elgg\Exceptions\Http\EntityNotFoundException;

$maps_provider = event_manager_get_maps_provider();
if ($maps_provider === 'none') {
	throw new EntityNotFoundException();
}

$page_owner = elgg_extract('page_owner', $vars);

$body = elgg_format_element('div', [
	'id' => 'event_manager_onthemap_canvas',
	'data-resource' => elgg_extract('resource', $vars),
	'data-guid' => ($page_owner instanceof \ElggEntity) ? $page_owner->guid : null,
	'data-tag' => get_input('tag'),
]);

echo elgg_view('event_manager/listing/elements/tags');

echo elgg_format_element('div', ['id' => 'event_manager_event_map'], $body);

echo elgg_format_element('script', [], 'require(["elgg/hooks", "event_manager/maps/' . $maps_provider . '/onthemap"], function(hooks) {
	hooks.trigger("tab:onthemap", "event_manager");
});');
